/*
1. show the form only when user press the add/edit button
2. prefill the edit form
*/

let cat_add_button = document.getElementById('cat_add_button')
if (cat_add_button) {
    let cat_db = document.getElementsByClassName('cat-row')
    let cat_del_form = document.getElementById('cat_del')
    let cat_edit_fieldset = document.getElementById('cat_edit').parentElement
    let cat_edit_cid = document.getElementById('cat_edit_cid')
    let cat_edit_name = document.getElementById('cat_edit_name')
    let cat_add_fieldset = document.getElementById('cat_insert').parentElement

    for (const row of cat_db) {
        let cid = row.children[2].textContent
        let name = row.children[3].textContent
        // del button
        row.children[0].firstElementChild.onclick = () => {
            let d = confirm(`Are you sure you want to delete category "${name}"?`);
            if (d) {
                cat_del_form.firstElementChild.value = cid
                cat_del_form.submit()
            }
        }
        // edit button
        row.children[1].firstElementChild.onclick = () => {
            cat_edit_cid.value = cid
            cat_edit_name.value = name
            cat_edit_fieldset.classList.remove('d-none')
            cat_add_fieldset.classList.add('d-none')
            cat_edit_fieldset.scrollIntoView();
        }
    }

    // add button
    cat_add_button.onclick = () => {
        cat_edit_fieldset.classList.add('d-none')
        cat_add_fieldset.classList.remove('d-none')
        cat_add_fieldset.scrollIntoView();
    }
}


let prod_add_button = document.getElementById('prod_add_button')
if (prod_add_button) {
    let prod_db = document.getElementsByClassName('prod-row')
    let prod_del_form = document.getElementById('prod_del')
    let prod_edit_fieldset = document.getElementById('prod_edit').parentElement
    let prod_add_fieldset = document.getElementById('prod_insert').parentElement
    let prod_edit_inputs = document.getElementsByClassName('prod-edit-input')

    for (const row of prod_db) {
        // del button
        row.children[0].firstElementChild.onclick = () => {
            let d = confirm(`Are you sure you want to delete product "${row.children[4].textContent}"?`);
            if (d) {
                prod_del_form.firstElementChild.value = row.children[2].textContent // pid
                prod_del_form.submit()
            }
        }
        // edit button
        row.children[1].firstElementChild.onclick = () => {
            for (let i=0; i<prod_edit_inputs.length; i++) {
                prod_edit_inputs[i].value = row.children[i+2].textContent
            }
            prod_edit_inputs[1].value = row.children[3].dataset.cid
            prod_edit_fieldset.classList.remove('d-none')
            prod_add_fieldset.classList.add('d-none')
            prod_edit_fieldset.scrollIntoView();
        }
    }

    // add button
    prod_add_button.onclick = () => {
        prod_edit_fieldset.classList.add('d-none')
        prod_add_fieldset.classList.remove('d-none')
        prod_add_fieldset.scrollIntoView();
    }

    let prod_edit_file = document.getElementById('prod_edit_file')
    let prod_add_file = document.getElementById('prod_insert_file')

    function check_file_size(e) {
        let prod_file = e.currentTarget
        let drop_area_container = prod_file.parentElement.parentElement
        let prod_alert = drop_area_container.getElementsByClassName('size-alert')[0]
        let filename_text = drop_area_container.getElementsByClassName('filename-text')[0]
        let imgElement = drop_area_container.getElementsByClassName('preview-thumbnail')[0]

        if (prod_file.files.length) {
            const file = prod_file.files[0]
            const fileSize = file.size

            // check file size
            if (fileSize > 5*1024*1024) {
                prod_alert.classList.remove('d-none')
            } else {
                prod_alert.classList.add('d-none')
            }

            // show file name
            filename_text.textContent = file.name

            // show preview image
            let reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onloadend = () => {
                imgElement.src = reader.result
                imgElement.classList.remove('d-none')
            }
        } else {
            imgElement.classList.add('d-none')
            prod_alert.classList.add('d-none')
            filename_text.textContent = ''
        }
    }

    prod_edit_file.onchange = check_file_size
    prod_add_file.onchange = check_file_size


    // drag and drop
    let dropAreas = document.getElementsByClassName('drop_area')

    function drag_enter_over_handler(e) {
        //e.preventDefault()
        e.currentTarget.classList.add('drag-effect');
    }
    function drag_leave_and_drop_handler(e) {
        //e.preventDefault()
        e.currentTarget.classList.remove('drag-effect');
    }

    for (const e of dropAreas) {
        e.ondragenter = drag_enter_over_handler
        e.ondragover = drag_enter_over_handler
        e.ondragleave = drag_leave_and_drop_handler
        e.ondrop = drag_leave_and_drop_handler
    }

    // hide original interface if javascript is supported
    for (const file_input of document.getElementsByClassName('file-input')) {
        file_input.classList.add('hidden-file-input')
    }
}
