
function doAmazingThings() {
  alert('YOU AM AMAZING!');
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('amazing')
	.addEventListener('click', doAmazingThings);
});

