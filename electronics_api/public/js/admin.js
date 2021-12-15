// /***********************************************************************************************************
//  ******                            Show All Messages for Admin                                        ******
//  **********************************************************************************************************/
//
// //This function gets called when the Admin link in the nav bar is clicked. It shows all the records of tvs
// function showAllPhones() {
//     const url = baseUrl_API + "/phones?limit=50";
//     fetch(url, {
//         method: 'GET',
//         headers: {"Authorization": "Bearer " + jwt}
//     })
//         .then(checkFetch)
//         .then(response => response.json())
//         .then(posts => displayPhones(posts.data))
//         .catch(err => showMessage("Errors", err)) //display errors
// }
//
//
// //Callback function that shows all the messages. The parameter is an array of messages.
// // The first parameter is an array of messages and second parameter is the subheading, defaults to null.
// function displayPhones(phones, subheading=null) {
//     console.log("display all tvs for the editing purpose")
//
//     // search box and the row of headings
//     let _html = `<div style='text-align: right; margin-bottom: 3px'>
//         <input id='search-term' placeholder='Enter search terms'>
//         <button id='btn-post-search' onclick='searchPhones()'>Search</button></div>
//         <div class='content-row content-row-header'>
//         <div class='phone-id'>Phone ID</div>
//         <div class='phone-provider_id'>Provider ID</div>
//         <div class='phone-model'>Model</div>
//         <div class='phone-brand'>Brand</div>
//         <div class='phone-price'>Price</div>
//             </div>`;  //end the row
//
//     // content rows
//     for (let x in phones) {
//         let phone = phones[x];
//         let cssClass = (x % 2 == 0) ? 'content-row' : 'content-row content-row-odd';
//         _html += `<div id='content-row-${phone.phone_id}' class='${cssClass}'>
//             <div class='phone-id'>${phone.phone_id}</div>
//             <div class='phone-provider_id'>${phone.provider_id}</div>
//             <div class='phone-name'>${phone.name}</div>
//             <div class='phone-brand'>${phone.brand}</div>
//             <div class='phone-price'>${phone.price}</div>
//             </div>`;
//
//
//             _html += `<div class='list-edit'><button id='btn-tv-edit-${tv.tv_id}' onclick=editTV('${tv.tv_id}') class='btn-light'> Edit </button></div>
//             <div class='list-update'><button id='btn-tv-update-${tv.tv_id}' onclick=updateTV('${tv.tv_id}') class='btn-light btn-update' style='display:none'> Update </button></div>
//             <div class='list-delete'><button id='btn-tv-delete-${tv.tv_id}' onclick=deleteTV('${post.id}') class='btn-light'>Delete</button></div>
//             <div class='list-cancel'><button id='btn-tv-cancel-${tv.tv_id}' onclick=cancelUpdateTV('${post.id}') class='btn-light btn-cancel' style='display:none'>Cancel</button></div>`
//
//         _html += '</div>';  //end the row
//     }
//
//     //the row of element for adding a new message
//
//         _html += `<div class='content-row' id='tv-add-row' style='display: none'>
//             <div class='tv-tv_id tv-editable' id='tv-new-provider_id' contenteditable='true' content="User ID"></div>
//             <div class='tv-name tv-editable' id='tv-new-name' contenteditable='true'></div>
//             <div class='tv-brand tv-editable' id='tv-new-brand' contenteditable='true'></div>
//             <div class='tv-price tv-editable' id='tv-new-price' contenteditable='true'></div>
//             <div class='list-update'><button id='btn-add-tv-insert' onclick='addTV()' class='btn-light btn-update'> Insert </button></div>
//             <div class='list-cancel'><button id='btn-add-tv-cancel' onclick='cancelAddTV()' class='btn-light btn-cancel'>Cancel</button></div>
//             </div>`;  //end the row
//
//         // add new message button
//         _html += `<div class='content-row tv-add-button-row'><div class='tv-add-button' onclick='showAddRow()'>+ ADD A NEW TV</div></div>`;
//
//     //Finally, update the page
//     subheading = (subheading == null) ? 'All TVs' : subheading;
//     updateMain('TVs', subheading, _html);
// }
//
// /***********************************************************************************************************
//  ******                            Search Messages                                                    ******
//  **********************************************************************************************************/
// function searchTVs() {
//    console.log('searching for TVs');
//
//     let term = $("#search-term").val();
// //console.log(term);
//     const url = baseUrl_API + "/tvs?q=" + term;
//     let subheading = '';
// //console.log(url);
//     if (term == '') {
//         subheading = "All TVs";
//     } else if (isNaN(term)) {
//         subheading = "TV Containing '" + term + "'"
//     } else {
//         subheading = "TV whose ID is having" + term;
//     }
// //send the request
//     fetch(url, {
//         method: 'GET',
//         headers: {"Authorization": "Bearer " + jwt}
//     })
//         .then(checkFetch)
//         .then(response => response.json())
//         .then(tvs => displayAllTVs(tvs))
//         .catch(err => showMessage("Errors", err)) //display errors
//
//
// }
//
//
// /***********************************************************************************************************
//  ******                            Edit a Message                                                     ******
//  **********************************************************************************************************/
//
// // This function gets called when a user clicks on the Edit button to make items editable
// function editTV(tv_id) {
//     //Reset all items
//     resetTV();
//
//     //select all divs whose ids begin with 'post' and end with the current id and make them editable
//     // $("div[id^='post-edit'][id$='" + id + "']").each(function () {
//     //     $(this).attr('contenteditable', true).addClass('post-editable');
//     // });
//
//     $("div#tv-edit-name-" + tv_id).attr('contenteditable', true).addClass('tv-editable');
//     $("div#tv-edit-brand-" + tv_id).attr('contenteditable', true).addClass('tv-editable');
//     $("div#tv-edit-price-" + tv_id).attr('contenteditable', true).addClass('tv-editable');
//
//     $("button#btn-post-edit-" + tv_id + ", button#btn-tv-delete-" + tv_id).hide();
//     $("button#btn-post-update-" + tv_id + ", button#btn-tv-cancel-" + tv_id).show();
//     $("div#tv-add-row").hide();
// }
//
// //This function gets called when the user clicks on the Update button to update a message record
// function updateTV(tv_id) {
// 	console.log('update the TV whose id is ' + tv_id);
//
//     let data = {};
//     data['provider_id'] = $("div#tv-edit-provider_id-" + tv_id).html();
//     data['name'] = $("div#tv-edit-name-" + tv_id).html();
//     data['brand'] = $("div#tv-edit-brand-" + tv_id).html();
//     data['price'] = $("div#tv-edit-price" + tv_id).html();
//     console.log(data);
//     const url = baseUrl_API + "/tvs/" + tv_id;
//     console.log(url);
//     fetch(url, {
//         method: 'PATCH',
//         headers: {
//             "Authorization": "Bearer " + jwt,
//             "Content-Type": "application/json"
//         },
//         body: JSON.stringify(data)
//     })
//         .then(checkFetch)
//         .then(() => resetTV())
//         .catch(error => showTV("Errors", error))
// }
//
//
// //This function gets called when the user clicks on the Cancel button to cancel updating a message
// function cancelUpdateTV(tv_id) {
//     showAllTVs();
// }
//
// /***********************************************************************************************************
//  ******                            Delete a Message                                                   ******
//  **********************************************************************************************************/
//
// // This function confirms deletion of a message. It gets called when a user clicks on the Delete button.
// function deleteTV(tv_id) {
//     $('#modal-button-ok').html("Delete").show().off('click').click(function () {
//         removeTV(tv_id);
//     });
//     $('#modal-button-close').html('Cancel').show().off('click');
//     $('#modal-title').html("Warning:");
//     $('#modal-content').html('Are you sure you want to delete the TV?');
//
//     // Display the modal
//     $('#modal-center').modal();
// }
//
// // Callback function that removes a message from the system. It gets called by the deletePost function.
// function removeTV(tv_id) {
// 	console.log('remove the TV whose id is ' + tv_);
//
//     let url = baseUrl_API + "/tvs/" + tv_id;
//     fetch(url, {
//         method: 'DElETE',
//         headers: {"Authorization": "Bearer " + jwt,},
//     })
//         .then(checkFetch)
//         .then(() => showAllTVs())
//         .catch(error => showTV("Errors", error))
// }
//
//
// /***********************************************************************************************************
//  ******                            Add a Message                                                      ******
//  **********************************************************************************************************/
// //This function shows the row containing editable fields to accept user inputs.
// // It gets called when a user clicks on the Add New Student link
// function showAddRow() {
//     resetTV(); //Reset all items
//     $('div#tv-add-row').show();
// }
//
// //This function inserts a new message. It gets called when a user clicks on the Insert button.
// function addTV() {
// 	console.log('Add a new TV');
//
//     let data = {};
//     $("div[tv_id^='tv-new-']").each(function () {
//         let field = $(this).attr('tv_id').substr(9);
//         let value = $(this).html();
//         data[field] = value;
//     });
// // data['name'] = $("div#post-new-name").html();
//     console.log(data);
//     const url = baseUrl_API + "/tvs";
//     console.log(url);
//     fetch(url, {
//         method: 'POST',
//         headers: {
//             "Authorization": "Bearer " + jwt,
//             "Content-Type": "application/json"
//         },
//         body: JSON.stringify(data)
//     })
//         .then(checkFetch)
//         .then(() => showAllTVs())
//         .catch(err => showTV("Errors", err))
// }
//
//
//
// // This function cancels adding a new TV. It gets called when a user clicks on the Cancel button.
// function cancelAddTV() {
//     $('#tv-add-row').hide();
// }
//
// /***********************************************************************************************************
//  ******                            Check Fetch for Errors                                             ******
//  **********************************************************************************************************/
// /* This function checks fetch request for error. When an error is detected, throws an Error to be caught
//  * and handled by the catch block. If there is no error detetced, returns the promise.
//  * Need to use async and await to retrieve JSON object when an error has occurred.
//  */
// let checkFetch = async function (response) {
//     if (!response.ok) {
//         await response.json()  //need to use await so Javascipt will until promise settles and returns its result
//             .then(result => {
//                 throw Error(JSON.stringify(result, null, 4));
//             });
//     }
//     return response;
// }
//
//
// /***********************************************************************************************************
//  ******                            Reset post section                                                 ******
//  **********************************************************************************************************/
// //Reset TV section: remove editable features, hide update and cancel buttons, and display edit and delete buttons
// function resetTV() {
//     // Remove the editable feature from all divs
//     $("div[tv_id^='tv-edit-']").each(function () {
//         $(this).removeAttr('contenteditable').removeClass('tv-editable');
//     });
//
//     // Hide all the update and cancel buttons and display all the edit and delete buttons
//     $("button[tv_id^='btn-post-']").each(function () {
//         const tv_id = $(this).attr('tv_id');
//         if (tv_id.indexOf('update') >= 0 || tv_id.indexOf('cancel') >= 0) {
//             $(this).hide();
//         } else if (tv_id.indexOf('edit') >= 0 || tv_id.indexOf('delete') >= 0) {
//             $(this).show();
//         }
//     });
// }