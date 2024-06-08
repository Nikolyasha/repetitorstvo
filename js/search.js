openList.forEach(el => el.click());

function openFilter(event){
	let block = $(event.parentElement);
	let block_title = block[0].getElementsByClassName("positions")[0];
	let list = $(block[0].getElementsByClassName("dropdown_block")[0]);
	if(block.height() > 41){
		block_title.classList.remove("positions_active");
		block.animate({"height": 41});
	}
	else {
		console.log(list.height() + 41);
		block_title.classList.add("positions_active");
		block.animate({"height": list.height() + 82});
	}
}

function changeSort(target){
	document.forms[0].order.value = target;
	document.forms[0].submit();
}

function select_page(page){
    if(+page == NaN){
        return;
    }
    if(+page < 0){
        try{
            page = +prompt("Номер страницы к которой перейти", "1") - 1;
            if(page > 0 && page != NaN){
                $('#filter_form')[0].page.value=page;
                $('#filter_form')[0].submit();
            }
        }
        catch(e){
            alert("Вводите только числа");
        }
    }
    else{
        $('#filter_form')[0].page.value=+page;
        $('#filter_form')[0].submit();
    }
}
