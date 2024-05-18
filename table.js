const table_elements = Array.from(document.getElementsByClassName('row'));

table_elements.forEach(element => {
    if (element.scrollHeight > element.clientHeight) {
        element.classList.add('overflowed');
    }
});
