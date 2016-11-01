var markbox = new function(){
	var that = this;
	this.fileList = function(dir){
		$('#list').html();
		var html = '';
		$.ajax({
			url:"./ajax.php?mod=file&dir="+dir,
			method:"GET",
			dataType:"JSON",
			success:function(rsp){
				var list = rsp.data;
				for(var i in list){
					html += '<a href="#" class="list-group-item" data-type="read" data-val="'+list[i].file+'"><i class="glyphicon glyphicon-file"></i> '+list[i].title+'</a>';
				}
				$('#list').html(html);
			}
		});
	}

	this.foldList = function(dir){
		var html = '';
		$.ajax({
			url:"./ajax.php?mod=fold&dir="+dir,
			method:"GET",
			dataType:"JSON",
			success:function(rsp){
				var list = rsp.data;
				for(var i in list){
					html += '<a href="#" class="list-group-item" data-type="file" data-val="'+list[i].file+'"><i class="glyphicon glyphicon-folder-close"></i> '+list[i].title+'</a>';
				}
				$('#list').html(html);
			}

		});
	}

	this.showMark = function(file){
		$.ajax({
			url:'./ajax.php?mod=read&file='+file,
			method:"GET",
			dataType:'JSON',
			success:function(rsp){
				var data = rsp.data;
				$('#content').html(data);
			}
		});
	}

	this.maskview = function(ele){
		var maskview = document.createElement('div');
		maskview.className = 'maskview';
		maskview.innerHTML = ele;
		$('body')[0].appendChild(maskview);
		$(maskview).slideDown('slow');
	}

	this.maskviewClose = function(){
		$('.maskview').slideUp('fast',function(){
			$(this).remove();
		});
	}

	this.fileList('all');
	
	$('#markbox-menu .list-group-item:gt(0)').click(function(){
		$('#markbox-menu .active').removeClass('active');
		$(this).addClass('active');
		var type = $(this).attr('data-type');
		var val = $(this).attr('data-val');
		var title = $(this).attr('title');
		$('#current').html(title);
		if(type == 'file'){
			that.fileList(val);
		}else{
			that.foldList(val);
		}
	});

	$('#list').on('click','a',function(){
		$('#markbox-list .active').removeClass('active');
		$(this).addClass('active');
		var type = $(this).attr('data-type');
		var val = $(this).attr('data-val');
		if(type == 'file'){
			that.fileList(val);
		}else if(type == 'read'){
			that.showMark(val);
		}else{
			that.foldList(val);
		}
	});
	
	$('body').on('click','#maskviewClose',this.maskviewClose);
	
	var testEditor = null;
	$('#write').click(function(){
		$.ajax({
			url:'static/plugs/write.html',
			method:'GET',
			success:function(html){
				that.maskview(html);
				testEditor = editormd("test-editormd", {
					width:"100%",
					height:920,
					syncScrolling:"single",
					path:"static/editor/lib/",
					imageUpload:true,
					imageFormats:["jpg", "jpeg", "gif", "png", "bmp"],
					imageUploadURL:"/php/upload.php",
					emoji:true,
					tex:true,
					flowChart:true,
					watch:false
					//toolbar:false,
				});
			}
		})
	});


}