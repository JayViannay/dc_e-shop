document.getElementById('select-address').addEventListener('change', e => {
    console.log('Hello From app_user_profile');
    displayAddressForm(e.target.value);
});

document.getElementById('js_close').addEventListener('click', e => {
    toggleBtnAddressForm();
});