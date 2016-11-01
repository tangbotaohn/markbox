var testEditor;
$(function() {
testEditor = editormd("test-editormd", {
		width : "100%",
		height : 720,
		syncScrolling : "single",
		path : "/theme/js/editor/lib/",
		imageUpload : true,
		imageFormats : ["jpg", "jpeg", "gif", "png", "bmp"],
		imageUploadURL : "/php/upload.php"
	});
});