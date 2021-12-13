/***********************************************************************************************************
 ******                            Show Posts                                                         ******
 **********************************************************************************************************/
//This function shows all posts. It gets called when a user clicks on the Post link in the nav bar.

// Pagination, sorting, and limiting are disabled
// function showPosts () {
//console.log('show all messages');
//const url = baseUrl_API + '/provider';
//define AXIOS request
//axios({
//method: 'get',
//url: url,
//cache: true,
//headers: {"Authorization": "Bearer " + jwt}
//})
//.then(function (response) {
//displayPosts(response.data);
//})
//.catch(function (error) {
//handleAxiosError(error);
//});

//}

function showProviders (offset = 0) {
    //if the selection list exists, retrieve the selected option value;otherwise, set a default value.
    let limit = ($("#post-limit-select").length) ? $('#post-limit-select option:checked').val() : 5;
    let sort = ($("#post-sort-select").length) ? $('#post-sort-select option:checked').val() : "provider_id:asc";
    //construct the request url
    const url = baseUrl_API + '/providers?limit=' + limit + "&offset=" + offset + "&sort=" + sort;
}

function showTVs () {
    const url = baseUrl_API + '/tvs';
//define AXIOS request
    axios({
        method: 'get',
        url: url,
        cache: true,
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(function (response) {
            displayTVs(response.data);
        })
        .catch(function (error) {
            handleAxiosError(error);
        });
}
function showPhones () {
    const url = baseUrl_API + '/phones';
//define AXIOS request
    axios({
        method: 'get',
        url: url,
        cache: true,
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(function (response) {
            displayPhones(response.data);
        })
        .catch(function (error) {
            handleAxiosError(error);
        });
}

//Callback function: display all posts; The parameter is a promise returned by axios request.
function displayProviders (response) {
    //console.log(response);
    let _html;
    _html =
        "<div class='content-row content-row-header'>" +
        "<div class='post-id'>Provider ID</></div>" +
        "<div class='post-body'>Provider Name</></div>" +
        "<div class='post-create'>City</div>" +
        "<div class='post-update'>State</div>" +
        "<div class='post-update'>Phone Number</div>"
        "</div>";
    let providers = response.data;
    providers.forEach(function(providers, x){
        let cssClass = (x % 2 == 0) ? 'content-row' : 'content-row content-row-odd';
        _html += "<div class='" + cssClass + "'>" +
            "<div class='post-id'>" +
            "<span class='list-key' onclick=showComments('" + provider.provider_id + "') title='Get post details'>" + provider.provider_id + "</span>" +
            "</div>" +
            "<div class='post-body'>" + provider.name + "</div>" +
            "<div class='post-create'>" + provider.city + "</div>" +
            "<div class='post-update'>" + provider.state + "</div>" +
            "<div class='post-update'>" + provider.phone_number + "</div>" +
            "</div>" +
            "<div class='container post-detail' id='post-detail-" + provider.provider_id + "' style='display: none'></div>";
    });

    //Finally, update the page
    //Add a div block for pagination links and selection lists for limiting and sorting courses
    _html += "<div class='content-row course-pagination'><div>";
    //pagination
    _html += paginateProviders(response);
    //items per page
    _html += limitProviders(response);
    //sorting
    _html += sortProviders(response);
    //end the div block
    _html += "</div></div>";
    updateMain('Providers', 'All Providers', _html);
}


/***********************************************************************************************************
 ******                            Show TVS and Phones associated with a Provider                                ******
 **********************************************************************************************************/
/* Display all comments. It get called when a user clicks on a message's id number in
 * the message list. The parameter is the message id number.
*/
function showTVsfromProvider(number) {
    console.log('get all TVs from a provider');

    let url = baseUrl_API + '/providers/' + number + '/tvs';
    axios({
        method: 'get',
        url: url,
        cache: true,
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(function (response) {
//console.log(response.data);
            displayTVs(number, response);
        })
        .catch(function (error) {
            handleAxiosError(error);
        });

}

function showPhonesfromProvider(number) {
    console.log('get all phones from a provider');

    let url = baseUrl_API + '/providers/' + number + '/phones';
    axios({
        method: 'get',
        url: url,
        cache: true,
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(function (response) {
//console.log(response.data);
            displayPhones(number, response);
        })
        .catch(function (error) {
            handleAxiosError(error);
        });

}


// Callback function that displays all details of a course.
// Parameters: course number, a promise
function displayTVs(number, response) {
    let _html = "<div class='content-row content-row-header'>TVs</div>";
    let tvs = response.data;
    //console.log(number);
    //console.log(tvs);
    tvs.forEach(function(tv, x){
        _html +=
            "<div class='post-detail-row'><div class='post-detail-label'>TV ID</div><div class='post-detail-field'>" + tv.tv_id + "</div></div>" +
            "<div class='post-detail-row'><div class='post-detail-label'>Name</div><div class='post-detail-field'>" + tv.name + "</div></div>" +
            "<div class='post-detail-row'><div class='post-detail-label'>Brand</div><div class='post-detail-field'>" + tv.brand + "</div></div>";
        "<div class='post-detail-row'><div class='post-detail-label'>Price</div><div class='post-detail-field'>" + tv.price + "</div></div>";
    });

    $('#post-detail-' + number).html(_html);
    $("[id^='post-detail-']").each(function(){   //hide the visible one
        $(this).not("[id*='" + number + "']").hide();
    });

    $('#post-detail-' + number).toggle();
}



function displayPhones(number, response) {
    let _html = "<div class='content-row content-row-header'>Phones</div>";
    let phones = response.data;
    //console.log(number);
    //console.log(phone);
    phones.forEach(function(phone, x){
        _html +=
            "<div class='post-detail-row'><div class='post-detail-label'>Phone ID</div><div class='post-detail-field'>" + phone.phone_id + "</div></div>" +
            "<div class='post-detail-row'><div class='post-detail-label'>Name</div><div class='post-detail-field'>" + phone.name + "</div></div>" +
            "<div class='post-detail-row'><div class='post-detail-label'>Brand</div><div class='post-detail-field'>" + phone.brand + "</div></div>";
        "<div class='post-detail-row'><div class='post-detail-label'>Price</div><div class='post-detail-field'>" + phone.price + "</div></div>";
    });

    $('#post-detail-' + number).html(_html);
    $("[id^='post-detail-']").each(function(){   //hide the visible one
        $(this).not("[id*='" + number + "']").hide();
    });

    $('#post-detail-' + number).toggle();
}
/*******************************************************************************
 *********************
 *********                  Paginating, sorting, and limiting providers
 **********

 ********************************************************************************
 *******************/


//paginate all providers
function paginateProviders(response) {
    //calculate the total number of pages
    let limit = response.limit;
    let totalCount = response.totalCount;
    let totalPages = Math.ceil(totalCount / limit);

    //determine the current page showing
    let offset = response.offset;
    let currentPage = offset / limit + 1;

    //retrieve the array of links from response json
    let links = response.links;

    //convert an array of links to JSON document. Keys are "self", "prev", "next", "first", "last"; values are offsets.
    let pages = {};

    //extract offset from each link and store it in pages
    links.forEach(function (link) {
        let href = link.href;
        let offset = href.substr(href.indexOf('offset') + 7);
        pages[link.rel] = offset;
    });
    if (!pages.hasOwnProperty('prev')) {
        pages.prev = pages.self;
    }
    if (!pages.hasOwnProperty('next')) {
        pages.next = pages.self;
    }

    //generate HTML code for links
    let _html = `Showing Page ${currentPage} of 
${totalPages}&nbsp;&nbsp;&nbsp;&nbsp;
                <a href='#course' title="first page" 
onclick='showProviders(${pages.first})'> << </a>
                <a href='#course' title="previous page" 
onclick='showProviders(${pages.prev})'> < </a>
                <a href='#course' title="next page" 
onclick='showProviders(${pages.next})'> > </a>
                <a href='#course' title="last page" 
onclick='showProvoders(${pages.last})'> >> </a>`;
    return _html;
}

//limit providers
function limitProviders(response) {
    //define an array of courses per page options
    let postsPerPageOptions = [5, 10, 20];

    //create a selection list for limiting courses
    let _html = `&nbsp;&nbsp;&nbsp;&nbsp; Items per page:<select id='post-limit-select' onChange='showProviders()'>`;
    postsPerPageOptions.forEach(function (option) {
        let selected = (response.limit == option) ? "selected" : "";
        _html += `<option ${selected} value="${option}">${option}</option>`;
    });

    _html += "</select>";
    return _html;
}

//sort providers
function sortProviders(response) {
    //create selection list for sorting
    let sort = response.sort;
    //sort field and direction: convert json to a string then remove {, }, and "
    let sortString = JSON.stringify(sort).replace(/["{}]+/g, "");
    console.log(sortString);

    //define a JSON containing sort options
    let sortOptions = {
        "provider_id:asc": "First Provider ID -> Last Provider ID",
        "provider_id:desc": "Last Provider ID -> First Provider ID",
        "name:asc": "Provider name A -> Z",
        "name:desc": "Provider name Z -> A"
    };
    //create the selection list
    let _html = "&nbsp;&nbsp;&nbsp;&nbsp; Sort by: <select provider_id='post-sort- select'" + "onChange='showProviders()'>";
    for (let option in sortOptions) {
        let selected = (option == sortString) ? "selected" : "";
        _html += `<option ${selected} value='${option}'>${sortOptions[option]} </option>`;
    }
    _html += "</select>";
    return _html;
}

