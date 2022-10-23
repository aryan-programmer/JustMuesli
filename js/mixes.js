function htmlSetup() {
	let $$deleteButtons = document.getElementsByClassName("mix-action-delete");

	function onDeleteButtonClick(ev) {
		let $$delBtn = ev.target;
		let name     = $$delBtn.getAttribute("data-mix-name");
		let id       = $$delBtn.getAttribute("data-mix-id");
		let deleteQ  = confirm(`Are you sure you want to delete the mix ${name}`);
		if (deleteQ === true) {
			window.location.href = `delete_mix.php?id=${id}`;
		}
	}

	for (let $$deleteButton of $$deleteButtons) {
		$$deleteButton.onclick = onDeleteButtonClick;
	}
}
