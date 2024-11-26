const input = document.getElementById("image-input")
console.log("DETAIL", document.querySelector("#pick-image"))
/*pick.addEventListener('click', () => {
	console.log('hey')
	/*if(input.fireEvent) {
		input.fireEvent('onclick')
	}else{
		var evObj = document.createEvent('Events');
	evObj.initEvent('click', true, false);
	input.dispatchEvent(evObj);
	}
})*/


input.addEventListener('change', () => {
	var reader = new FileReader()
	const preview = document.getElementById("profile-image")
	reader.onload = function() {
		preview.setAttribute('src', reader.result)
	}
	reader.readAsDataURL(event.target.files[0]);
});