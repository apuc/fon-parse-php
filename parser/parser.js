let search_title = arguments[0];
let search_coeffs = arguments[1];
let table_rows = document.getElementsByClassName("table__row");
for (let i = 0; i < table_rows.length; i++) {
    try {
        let title_block = table_rows[i].getElementsByClassName("table__match-title-text");
        if (title_block.length > 0) {
            let title = title_block[0].innerText;
            if (title == search_title) {
                let coeffs = {};
                let elem_coeffs = table_rows[i].getElementsByClassName('_type_btn');

                for (let j = 0; j < search_coeffs.length; j++) {
                    let index = search_coeffs[j];
                    coeffs[index] = elem_coeffs[index - 1].innerText;
                    if (coeffs[index] == '') {
                        coeffs[index] = 'Пусто';
                    }
                }
                return {
                    title: title,
                    coeffs: coeffs
                };
            }
        }
    } catch (e) {
    }
}
