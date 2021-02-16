let numProviders = numProcessed = percentStep = currPercentage = 0;
function searchProviders(providers, q, searchProviderBaseUrl, searchResultBaseUrl) {
    numProviders = providers.length;
    percentStep = Math.floor(100 / providers.length);
    let providersData = [];
    for (i = 0; i < providers.length; i++) {
        let currProvider = providers[i];
        if (currProvider['id'] === 3) continue;
        $.ajax({
            url: searchProviderBaseUrl + '/' + currProvider['id'] + '.json?' + q,
            dataType: 'json'
        }).always(function (result) {
            let data = result.res !== undefined ? result.res : {};
            if (data.num_pages > 1 && data.cursor === "") {
                // paginated result.  Search for next pages through parallel requests
                providersData[data.provider] = {};
                providersData[data.provider].successivePagesToProcess = data.num_pages - 1;
                for (j = 2; j <= data.num_pages; j++) {
                    $.ajax({
                        url: searchProviderBaseUrl + '/' + currProvider['id'] + '.json?' + q + '&page=' + j,
                        dataType: 'json'
                    }).always(function (data) {
                        providersData[data.provider].successivePagesToProcess--;
                        if (providersData[data.provider].successivePagesToProcess === 0) {
                            // request completed. Update progress
                            updateProgress();
                        }
                    });
                }
            } else if (data.num_pages > 1 && data.cursor !== "") {
                let url = searchProviderBaseUrl + '/' + currProvider['id'] + '.json',
                    page = '&page=' + data.cursor;
                execRequest(url, q, page, searchResultBaseUrl);
            } else {
                // request completed. Update progress
                updateProgress(searchResultBaseUrl);
            }
        });
    }
}

function execRequest(url, params, page, searchResultBaseUrl){
    $.ajax({
        url: url + '?' + params + page,
        dataType: 'json'
    }).always(function (result){
        let data = result.res !== undefined ? result.res : {};
        if(data.hasOwnProperty('cursor') && data.cursor !== ""){
            let page = '&page=' + data.cursor
            execRequest(url, params, page, searchResultBaseUrl);
            updateProgress();
        } else {
            updateProgress(searchResultBaseUrl);
        }
    });
}

function searchNextProvider(searchProviderBaseUrl) {

    let currProvider = providers.pop();
    numProcessed++;
    $('#provider-name').text( /*currProvider['Provider']['name'] +*/ ' (' + (numProcessed) + '/' + numProviders + ')');

    $.ajax({
        url: searchProviderBaseUrl + '/' + currProvider['Provider']['id'] + '.json?' + q,
        dataType: 'json'
    }).always(function () {
        // request completed. Update progress
        updateProgress();
    });
}

function updateProgress(url) {
    numProcessed++;
    currPercentage += percentStep;
    let $search = $('#search-progress');
    $search.attr('aria-valuenow', currPercentage);
    $search.attr('style', 'width:' + currPercentage + '%');
    $('#progress-percentage').text(currPercentage);

    if (url !== undefined) {
        // redirect to search results
        window.location = url;
    }
}
