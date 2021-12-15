// /***********************************************************************************************************
//  ******                            Show Phones                                                    ******
//  **********************************************************************************************************/
//This function shows all users. It gets called when a user clicks on the Users link in the nav bar.
function showPhones() {

    const url = baseUrl_API + "/phones?limit=50&offset=0&sort=phone_id:asc";
    fetch(url, {
        method: 'GET',
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(checkFetch)
        .then(response => response.json())
        .then(phones => displayPhones(phones.data))
        .catch(err => showMessage("Errors", err)) //display errors
}





//Callback function: display all phones; The parameter is an array of user objects.
function displayPhones(phones, subheading=null) {
    let _html;
    _html =
       `<div style='text-align: right; margin-bottom: 3px'>
        <input id='search-term' placeholder='Enter search terms'>
        <button id='btn-post-search' onclick='searchPhones()'>Search</button></div>           
        <div class='content-row content-row-header'>
        <div class='phone-id'>Phone ID</div>
        <div class='phone-provider_id'>Provider ID</div>
        <div class='phone-model'>Model</div>
        <div class='phone-brand'>Brand</div>
        <div class='phone-price'>Price</div>
        </div>`;
    for (let x in phones) {
        let phone = phones[x];
        _html += `<div class='content-row'>
            <div class='phone-id' id="phone-edit-id-${phone.phone_id}'">${phone.phone_id}</div>
            <div class='phone-provider_id' id="phone-edit-provider_id-${phone.phone_id}">${phone.provider_id}</div>
            <div class='phone-name' id="phone-edit-name-${phone.phone_id}">${phone.name}</div>
            <div class='phone-brand' id="phone-edit-brand-${phone.phone_id}">${phone.brand}</div>
            <div class='phone-price' id="phone-edit-price-${phone.phone_id}">${phone.price}</div>`


        _html += `<div class='list-edit'><button id='btn-phone-edit-${phone.phone_id}' onclick=editPhone('${phone.phone_id}') class='btn-light'> Edit </button></div>
            <div class='list-update'><button id='btn-phone-update-${phone.phone_id}' onclick=updatePhone('${phone.phone_id}') class='btn-light btn-update' style='display:none'> Update </button></div>
            <div class='list-delete'><button id='btn-phone-delete-${phone.phone_id}' onclick=deletePhone('${phone.phone_id}') class='btn-light'>Delete</button></div>
            <div class='list-cancel'><button id='btn-phone-cancel-${phone.phone_id}' onclick=cancelUpdatePhone('${phone.phone_id}') class='btn-light btn-cancel' style='display:none'>Cancel</button></div>`

        _html += '</div>';  //end the row
    }

    _html += `<div class='content-row' id='phone-add-row' style='display: none'> 
                <div class='phone-id phone-editable' id='phone_id' contenteditable='false' content="null"></div>
                <div class='phone-provider_id phone-editable' id='phone-new-provider_id' contenteditable='true'></div>
                <div class='phone-name phone-editable' id='phone-new-name' contenteditable='true'></div>
                <div class='phone-brand phone-editable' id='phone-new-brand' contenteditable='true'></div>
                <div class='phone-price phone-editable' id='phone-new-price' contenteditable='true'></div>
                <div class='list-update'><button id='btn-add-phone-insert' onclick='addPhone()' class='btn-light btn-update'> Insert </button></div>
                <div class='list-cancel'><button id='btn-add-phone-cancel' onclick='cancelAddPhone()' class='btn-light btn-cancel'>Cancel</button></div>
            </div>`;  //end the row



    // add new message button
    _html += `<div class='content-row phone-add-button-row'><div class='phone-add-button' onclick='showAddRow()'>+ ADD A NEW PHONE</div></div>`;

    //Finally, update the page
    subheading = (subheading == null) ? 'All Phones' : subheading;
    updateMain('Phones', 'All Phones', _html);
}

/*****************************************************
 ******** Pagination, sorting, and limiting TVs****
 *****************************************************/
//paginate courses
function paginatePhones(response) {
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
                <a href='#phone' title="first page" onclick'showPhones(${pages.first})'> << </a>
                <a href='#phone' title="previous page" onclick'showPhones(${pages.prev})'> < </a>
                <a href='#phone' title="next page" onclick'showPhones(${pages.next})'> > </a>
                <a href='#phone' title="last page" onclick'showPhones(${pages.last})'> >> </a>`

    return _html;
}

//limit courses
function limitPhones(response) {
    //define an array of courses per page options
    let coursesPerPageOptions = [5, 10, 20];

    //create a selection list for limiting courses
    let _html = `&nbsp;&nbsp;&nbsp;&nbsp; Items per page:<select id='course-limit-select' onChange='showPhones()'>`;
    coursesPerPageOptions.forEach(function(option) {
        let selected = (response.limit == option) ? "selected" : "";
        _html += `<option ${selected} value="${option}">${option}</option>`;

    })
    _html += "</select>";

    return _html;
}

//sort courses
function sortPhones(response) {
    //create the selection list for sorting
    let sort = response.sort;

    //sort field and direction: convert json to a string then remove characters
    let sortString = JSON.stringify(sort).replace(/["{}]+/g, "");

    //define a json containing sort options
    let sortOptions = {"number:asc": "Number A - Z",
        "number:desc": "Number Z - A",
        "title:asc": "Title A - Z",
        "title:desc": "Title Z - A"
    };
    //create selection list
    let _html = `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sort by:<select id='course-sort-select' onChange='showPhones()'>`;
    for(let option in sortOptions) {
        let selected = (option == sortString) ? "selected" : "";
        _html += `<option ${selected} value="${option}">${sortOptions[option]}</option>`;
    }

    _html += "</select>";
    return _html;
}


//This function gets called when the user clicks on the Cancel button to cancel updating a student
function cancelUpdatePhone(id) {
    showPhones();
}
/***********************************************************************************************************
 ******                            Delete TV                                                     ******
 **********************************************************************************************************/

// This function confirms deletion of a student. It gets called when a user clicks on the Delete button.
function deletePhone(id) {
    $('#modal-button-ok').html("Delete").show().off('click').click(function () {
        removePhone(id);
    });
    $('#modal-button-close').html('Cancel').show().off('click');
    $('#modal-content').html('Are you sure you want to delete the phone?');

    // Display the modal
    $('#modal-center').modal();
}

// Callback function that removes a student from the system. It gets called by the deleteStudent function.
function removePhone(id) {
    let url = baseUrl_API + "/phones/" + id;
    fetch(url, {
        method: 'DElETE',
        headers: {"Authorization": "Bearer " + jwt,},
    })
        .then(checkFetch)
        .then(() => showPhones())
        .catch(error => showMessage("Errors", error))
}

/***********************************************************************************************************
 ******                            Check Fetch for Errors                                             ******
 **********************************************************************************************************/
/* This function checks fetch request for error. When an error is detected, throws an Error to be caught
 * and handled by the catch block. If there is no error detetced, returns the promise.
 * Need to use async and await to retrieve JSON object when an error has occurred.
 */
let checkFetch = async function (response) {
    if (!response.ok) {
        await response.json()  //need to use await so Javascipt will until promise settles and returns its result
            .then(result => {
                throw Error(JSON.stringify(result, null, 4));
            });
    }
    return response;
}


/***********************************************************************************************************
 ******                            Add TV                                                        ******
 **********************************************************************************************************/
//This function shows the row containing editable fields to accept user inputs.
// It gets called when a user clicks on the Add New Student link
function showAddRow() {
    resetPhone(); //Reset all items
    $('div#phone-add-row').show();
}

//This function inserts a new student. It gets called when a user clicks on the Insert button.
function addPhone() {
    //console.log('Add a new student');
    let data = {};

    //retrieve new student details and create a JSON object
    $("div[id^='phone-new-']").each(function(){
        let field = $(this).attr('id').substr(10); //last part of an id is the field name, there are 12 characters b4 the field name
        let value = $(this).html(); //content of the div
        value = value.replace("<br>", "");
        data[field] = value;
    })


    //send the request via fetch
    const url = baseUrl_API + '/phones';
    console.log(url);

    fetch(url, {
        method: 'POST',
        headers: {
            "Authorization": "Bearer" + jwt,
            "Content-Type": 'application/json'
        },
        body: JSON.stringify(data),
    })
        .then(checkFetch)
        .then(() => showPhones())
        .catch(err => showPhones("Errors", err))
}



// This function cancels adding a new student. It gets called when a user clicks on the Cancel button.
function cancelAddPhone() {
    $('#phone-add-row').hide();
}

// This function gets called when a user clicks on the Edit button to make items editable
function editPhone(id) {
    //Reset all items
    resetPhone();

    //select all divs whose ids begin with 'post' and end with the current id and make them editable
    // $("div[id^='phone-edit'][id$='" + id + "']").each(function () {
    //     $(this).attr('contenteditable', true).addClass('phone-editable');
    // });

    $("div#phone-edit-id-" + id).attr('contenteditable', true).addClass('phone-editable');
    $("div#phone-edit-provider_id-" + id).attr('contenteditable', true).addClass('phone-editable');
    $("div#phone-edit-name-" + id).attr('contenteditable', true).addClass('phone-editable');
    $("div#phone-edit-brand-" + id).attr('contenteditable', true).addClass('phone-editable');
    $("div#phone-edit-price-" + id).attr('contenteditable', true).addClass('phone-editable');



    $("button#btn-phone-edit-" + id + ", button#btn-phone-delete-" + id).hide();
    $("button#btn-phone-update-" + id + ", button#btn-phone-cancel-" + id).show();
    $("div#phone-add-row").hide();
}

//This function gets called when the user clicks on the Update button to update a student record
function updatePhone(id) {
    let data = {};
    data['provider_id'] = $("div#phone-edit-provider_id-" + id).html();
    data['name'] = $("div#phone-edit-name-" + id).html();
    data['brand'] = $("div#phone-edit-brand-" + id).html();
    data['price'] = $("div#phone-edit-price-" + id).html();
    console.log(data);
    const url = baseUrl_API + "/phones/" + id;
    console.log(url);
    fetch(url, {
        method: 'PATCH',
        headers: {
            "Authorization": "Bearer " + jwt,
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
        .then(checkFetch)
        .then(() => resetPhone())
        .catch(error => showMessage("Errors", error))

    $("button#btn-phone-update-" + id + ", button#btn-phone-cancel-" + id).hide();
    $("button#btn-phone-edit-" + id + ", button#btn-phone-delete-" + id).show();

}



//Reset student section: remove editable features, hide update and cancel buttons, and display edit and delete buttons
function resetPhone() {
    // Remove the editable feature from all divs
    $("div[id^='phone-edit-']").each(function () {
        $(this).removeAttr('contenteditable').removeClass('phone-editable');
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

function searchPhones() {
    let term = $("#search-term").val();
    //console.log(term);
    const url = baseUrl_API + "/phones?q=" + term + "&offset=0&sort=phone_id:asc";
    let subheading = '';
    //console.log(url);
    if (term == '') {
        subheading = "All Phones";
    } else if (isNaN(term)) {
        subheading = "Phones Containing '" + term + "'"
    } else {
        subheading = "Phones whose ID includes" + term;
    }
    //send the request
    fetch(url, {
        method: 'GET',
        headers: {"Authorization": "Bearer " + jwt}
    })
        .then(checkFetch)
        .then(response => response.json())
        .then(phones => displayPhones(phones))
        .catch(err => showMessage("Errors", err)) //display errors
}