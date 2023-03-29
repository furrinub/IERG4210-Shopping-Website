const old_pw = document.getElementById('old_password')
const new_pw = document.getElementById('new_password_1')
const confirm_pw = document.getElementById('new_password_2')
const wrong_confirm_alert = document.getElementById('wrong_confirm_alert')
const same_alert = document.getElementById('same_alert')
const change_password_form = document.getElementById('change_password')

function check_same(e) {
    if (new_pw.value && confirm_pw.value && new_pw.value !== confirm_pw.value) {
        wrong_confirm_alert.classList.remove('d-none')
    } else {
        wrong_confirm_alert.classList.add('d-none')
    }
    if (old_pw.value && old_pw.value === new_pw.value && new_pw.value === confirm_pw.value) {
        same_alert.classList.remove('d-none')
    } else {
        same_alert.classList.add('d-none')
    }
}
new_pw.oninput = confirm_pw.oninput = check_same

change_password_form.onsubmit = (e) => {
    if (new_pw.value !== confirm_pw.value || old_pw.value === new_pw.value) {
        return false
    }
}

