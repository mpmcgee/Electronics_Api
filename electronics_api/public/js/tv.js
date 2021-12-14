// /***********************************************************************************************************
//  ******                            Show TVs                                                    ******
//  **********************************************************************************************************/
//This function shows all users. It gets called when a user clicks on the Users link in the nav bar.
function showTVs(offset =0) {

    let limit = ($('#course-limit-select').length) ? $('#course-limit-select option:checked').val() : 5;
    let sort = ($('#course-sort-select').length) ? $('#course-sort-select option:checked').val() : 'tv_id:asc';

    //console.log('Show all tvs');
    const url = baseUrl_API + '/tvs?limit=' + limit + "&offset=" + offset + "&sort=" + sort;

    axios({
        method: 'get',
        url: url,
        cache: true,
        headers: {"Authorization": "Bearer " + jwt}
    }).then(function(response) {
        displayTVs(response.data);
    }).catch(function(error) {
        handleAxiosError(error);
    });
}




//Callback function: display all tvs; The parameter is an array of user objects.
function displayTVs(response) {
    let _html;
    _html = `<div class='content-row content-row-header'>
        <div class='tv-tv_id'>TV ID</div>
        <div class='tv-provider_id'>Provider ID</div>
        <div class='tv-name'>Brand</div>
        <div class='tv-brand'>Brand</div>
        <div class='tv-price'>Price</div>
        </div>`;
    let tvs = response.data;
    tvs.forEach(function(tv, x) {
        let cssClass = (x % 2 == 0) ? 'content-row' : 'content-row content-row-odd';
        _html += `<div id='content-row-${tv.tv_id}' class='${cssClass}'>
            <div class='tv-tv_id'>${tv.tv_id}</div>
            <div class='tv-provider_id'>${tv.provider_id}</div>
            <div class='tv-n'>${tv.name}</div>
            <div class='user-username'>${tv.brand}</div>
            <div class='user-username'>${tv.price}</div>
            </div>`;
    });
        //add a div block for pagination links and selection lists for limiting and sorting courses
        _html += "<div class='content-row course-pagination'><div>";

        //pagination
        _html += paginateTVs(response);

        //limit courses
        _html += limitTVs(response);

        //sort courses
        _html += sortTVs(response);

        //close the div blocks
        _html += "</div></div>";

    //Finally, update the page
    updateMain('TVs', 'All Tvs', _html);
}

/*****************************************************
 ******** Pagination, sorting, and limiting TVs****
 *****************************************************/
//paginate courses
function paginateTVs(response) {
    //calculate total num of courses
    let limit = response.limit;
    let totalCount = response.totalCount;
    let totalPages = Math.ceil(totalCount/limit);

    //determine the current page showing
    let offset = response.offset;
    let currentPage = offset/limit + 1;

    //retrieve the array of links from the response json
    let links = response.links;

    //convert an array of links to JSON document; keys are "self", "prev", "next", "first", "last" vals are offsets
    let pages = {};

    //extract offset from each link and store it in pages
    links.forEach(function(link) {
        let href = link.href;
        let offset = href.substr(href.indexOf('offset') + 7);
        pages[link.rel] = offset;
    })

    if(!pages.hasOwnProperty('prev')) {
        pages.prev = pages.self;
    }

    if(!pages.hasOwnProperty('next')) {
        pages.next = pages.self;
    }

    //generate html code for links
    let _html = `Showing Page ${currentPage} of ${totalPages}&nbsp;&nbsp;&nbsp;&nbsp;
                <a href='#tv' title="first page" onclick'showTVs(${pages.first})'> << </a>
                <a href='#tv' title="previous page" onclick'showTVs(${pages.prev})'> < </a>
                <a href='#tv' title="next page" onclick'showTVs(${pages.next})'> > </a>
                <a href='#tv' title="last page" onclick'showTVs(${pages.last})'> >> </a>`

    return _html;
}

//limit courses
function limitTVs(response) {
    //define an array of courses per page options
    let tvsPerPageOptions = [5, 10, 20];

    //create a selection list for limiting courses
    let _html = `&nbsp;&nbsp;&nbsp;&nbsp; Items per page:<select id='course-limit-select' onChange='showTVs()'>`;
    tvsPerPageOptions.forEach(function(option) {
        let selected = (response.limit == option) ? "selected" : "";
        _html += `<option ${selected} value="${option}">${option}</option>`;

    })
    _html += "</select>";

    return _html;
}

//sort courses
function sortTVs(response) {
    //create the selection list for sorting
    let sort = response.sort;

    //sort field and direction: convert json to a string then remove characters
    let sortString = JSON.stringify(sort).replace(/["{}]+/g, "");

    //define a json containing sort options
    let sortOptions = {
        "tv_id:asc": "First TV ID -> Last TV ID",
        "id_id:desc": "Last TV ID -> First TV ID",
    };
    //create selection list
    let _html = `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sort by:<select id='course-sort-select' onChange='showTVs()'>`;
    for(let option in sortOptions) {
        let selected = (option == sortString) ? "selected" : "";
        _html += `<option ${selected} value="${option}">${sortOptions[option]}</option>`;
    }

    _html += "</select>";
    return _html;
}


//This function gets called when the user clicks on the Cancel button to cancel updating a student
function cancelUpdateTv(id) {
    showTVs();
}
/***********************************************************************************************************
 ******                            Delete TV                                                     ******
 **********************************************************************************************************/

// This function confirms deletion of a student. It gets called when a user clicks on the Delete button.
function deleteTV(id) {
    $('#modal-button-ok').html("Delete").show().off('click').click(function () {
        removeTV(tv_id);
    });
    $('#modal-button-close').html('Cancel').show().off('click');
    $('#modal-content').html('Are you sure you want to delete the student?');

    // Display the modal
    $('#modal-center').modal();
}

// Callback function that removes a student from the system. It gets called by the deleteStudent function.
function removeTV(id) {
    //console.log('remove the student whose id is ' + id);
    let url = baseUrl_API + '/tv' + tv_id;
    fetch(url, {
        method: 'DELETE',
        headers: {"Authorization": "Bearer" + jwt}
    })
        .then(checkFetch)
        .then(() => showTVs())
        .catch(err => showMessage("Errors", err))
}


/***********************************************************************************************************
 ******                            Add TV                                                        ******
 **********************************************************************************************************/
//This function shows the row containing editable fields to accept user inputs.
// It gets called when a user clicks on the Add New Student link
function showAddRow() {
    resetPlayer(); //Reset all items
    $('div#student-add-row').show();
}

//This function inserts a new student. It gets called when a user clicks on the Insert button.
function addTV() {
    //console.log('Add a new student');
    let data = {};

    //retrieve new student details and create a JSON object
    $("div[id^='student-new-']").each(function(){
        let field = $(this).attr('id').substr(2); //last part of an id is the field name, there are 12 characters b4 the field name
        let value = $(this).html(); //content of the div
        data[field] = value;
    })

    //send the request via fetch
    const url = baseUrl_API + '/tvs';

    fetch(url, {
        method: 'POST',
        headers: {
            "Authorization": "Bearer" + jwt,
            "Accept": 'application/json',
            "Content-Type": 'application/json'
        },
        body: JSON.stringify(data),
    })
        .then(checkFetch)
        .then(() => showPlayers())
        .catch(err => showMessage("Errors", err))
}



// This function cancels adding a new student. It gets called when a user clicks on the Cancel button.
function cancelAddTV() {
    $('#student-add-row').hide();
}

// This function gets called when a user clicks on the Edit button to make items editable
function editTV(id) {
    //Reset all items
    resetPlayer();

    //select all divs whose ids begin with 'student' and end with the current id and make them editable
    $("div[id^='student-edit'][id$='" + id + "']").each(function () {
        $(this).attr('contenteditable', true).addClass('student-editable');
    });

    $("button#btn-student-edit-" + id + ", button#btn-student-delete-" + id).hide();
    $("button#btn-student-update-" + id + ", button#btn-student-cancel-" + id).show();
    $("div#student-add-row").hide();
}

//This function gets called when the user clicks on the Update button to update a student record
function updateTV(id) {
    //console.log('update the student whose id is ' + id);
    let data = {};

    //select all divs whose ids begin with 'student-edit' and end with the currect id
    //then extract student details from the divs and create a JSON object
    $("div[id^='student-edit-'][id$='" + id + "']").each(function(){
        let field = $(this).attr('id').split('-')[2]; //the second part of an id is the field name
        let value = $(this).html(); //content of the div block
        data[field] = value;
    })

    //make fetch request to update the student
    const url = baseUrl_API + '/tvs' + id;
    fetch(url, {
        method: 'PUT',
        headers: {
            "Authorization": "Bearer" + jwt,
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
        .then(checkFetch)
        .then(() => resetTV()) //reset students
        .catch(err => showMessage("Errors", err))
}



//Reset student section: remove editable features, hide update and cancel buttons, and display edit and delete buttons
function resetTV() {
    // Remove the editable feature from all divs
    $("div[id^='student-edit-']").each(function () {
        $(this).removeAttr('contenteditable').removeClass('student-editable');
    });

    // Hide all the update and cancel buttons and display all the edit and delete buttons
    $("button[id^='btn-student-']").each(function () {
        const id = $(this).attr('id');
        if (id.indexOf('update') >= 0 || id.indexOf('cancel') >= 0) {
            $(this).hide();
        } else if (id.indexOf('edit') >= 0 || id.indexOf('delete') >= 0) {
            $(this).show();
        }
    });
}

function handleAxiosError(error) {
        let errMessage;
        if (error.response) {
    // The request was made and the server responded with a status code of 4xx or 5xx
            errMessage = {"Code": error.response.status, "Status":
                error.response.data.status};
        } else if (error.request) {
// The request was made but no response was received
            errMessage = {"Code": error.request.status, "Status":
                error.request.data.status};
        } else {
// Something happened in setting up the request that triggered an error
            errMessage = JSON.stringify(error.message, null, 4);
        }
        // showMessage('Error', errMessage);
}