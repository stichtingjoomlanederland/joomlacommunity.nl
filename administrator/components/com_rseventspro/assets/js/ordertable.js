Joomla.orderTable = function(listOrder) {
	table = document.getElementById("sortTable");
	direction = document.getElementById("directionTable");
	order = table.options[table.selectedIndex].value;
	
	if (order != listOrder) {
		dirn = 'asc';
	} else {
		dirn = direction.options[direction.selectedIndex].value;
	}
	
	Joomla.tableOrdering(order, dirn, '');
}