console.log('Hello From public/assets/js/_function.js');

const getAndDisplayColors = (ref, size) => {
    let colors = [];
    let idColors = [];

    const divSelectColor = document.getElementById('select_color_js');
    axios.post(`/reference/colors`, { 'ref_id': ref, 'size_id': size })
        .then(response => response.data)
        .then(data => {
            divSelectColor.innerHTML = "";

            const select = document.createElement('select');
            select.classList.add('form-select');
            select.name = "color";
            select.id = "js_select_color";

            const defaultOpt = document.createElement('option');
            defaultOpt.innerText = "Select color";
            defaultOpt.selected = true;

            select.appendChild(defaultOpt);
            divSelectColor.appendChild(select);

            colors = Object.values(data.colors);
            idColors = Object.keys(data.colors);

            for (let i = 0; i < colors.length; i++) {
                const opt = document.createElement('option');
                opt.value = idColors[i];
                opt.innerHTML = colors[i];
                opt.name = "color_id";
                select.appendChild(opt);
            }
            document.getElementById('js_select_color').addEventListener('change', e => {
                if (e.target.value !== "") {
                    document.getElementById('js_add_cart_btn').classList.remove('d-none');
                }
            });
        });
}

const displayAddressForm = (value) => {
    document.getElementById('js_close').classList.remove('d-none');
    document.getElementById('js_form_address').classList.remove('d-none');
    axios.get(`/api/profile/addresse/${value}`)
        .then(response => response.data)
        .then(data => {
            document.getElementById('address_name').value = data.address.name;
            document.getElementById('address_way').value = data.address.way;
            document.getElementById('address_zipcode').value = data.address.zipcode;
            document.getElementById('address_city').value = data.address.city;
            document.getElementById('address_country').value = data.address.country;
            document.getElementById('select-address').value = data.address.id;
        });
}

const toggleBtnAddressForm = () => {
    document.getElementById('js_form_address').classList.add('d-none');
    document.getElementById('js_close').classList.add('d-none');
}