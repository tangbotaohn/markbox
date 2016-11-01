var markbox = new function(){
	var that = this;
	
	this.mdfiles = function(dir){
		$('#list').html();
		var html = '';
		$.ajax({
			url:"./ajax.php?mod=mdfiles&t="+dir,
			method:"GET",
			dataType:"JSON",
			success:function(rsp){
				var list = rsp.data;
				for(var i in list){
					html += '<a href="#" class="list-group-item" data-mod="content" data-type="mdfiles" data-val="'+list[i].file+'"><i class="glyphicon glyphicon-file"></i> '+list[i].title+'</a>';
				}
				$('#list').html(html);
			}
		});
	}
	
	this.publish = function(){
		$('#list').html();
		var html = '';
		$.ajax({
			url:"./ajax.php?mod=publish&t=",
			method:"GET",
			dataType:"JSON",
			success:function(rsp){
				var list = rsp.data;
				for(var i in list){
					html += '<a href="#" class="list-group-item" data-mod="content" data-type="publish" data-val="'+list[i].file+'"><i class="glyphicon glyphicon-file"></i> '+list[i].title+'</a>';
				}
				$('#list').html(html);
			}
		});
	}
	
	this.recycles = function(){
		$('#list').html();
		var html = '';
		$.ajax({
			url:"./ajax.php?mod=recycles&t=",
			method:"GET",
			dataType:"JSON",
			success:function(rsp){
				var list = rsp.data;
				for(var i in list){
					html += '<a href="#" class="list-group-item" data-mod="content" data-type="recycles" data-val="'+list[i].file+'"><i class="glyphicon glyphicon-file"></i> '+list[i].title+'</a>';
				}
				$('#list').html(html);
			}
		});
	}
	
	this.mdfolds = function(dir){
		var html = '';
		$.ajax({
			url:"./ajax.php?mod=mdfolds&t="+dir,
			method:"GET",
			dataType:"JSON",
			success:function(rsp){
				var list = rsp.data;
				for(var i in list){
					html += '<a href="#" class="list-group-item" data-mod="mdfiles" data-val="'+list[i].file+'"><i class="glyphicon glyphicon-folder-close"></i> '+list[i].title+'</a>';
				}
				$('#list').html(html);
			}
		});
	}

	this.content = function(type,file){
		$.ajax({
			url:'./ajax.php?mod=content&type='+type+'&t='+file,
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

	this.mdfiles('');
	
	$('#markbox-menu .list-group-item:gt(0)').click(function(){
		$('#markbox-menu .active').removeClass('active');
		$(this).addClass('active');
		var mod = $(this).attr('data-mod');
		var type = $(this).attr('data-type');
		var val = $(this).attr('data-val');
		var title = $(this).attr('title');
		$('#current').html(title);
		if(mod == 'mdfiles'){
			that.mdfiles(val);
		}else if(mod == 'mdfolds'){
			that.mdfolds(val);
		}else if(mod == 'publish'){
			that.publish();
		}else if(mod == 'content'){
			that.content(type,val);
		}else if(mod == 'recycles'){
			that.recycles();
		}
	});

	$('#list').on('click','a',function(){
		$('#markbox-list .active').removeClass('active');
		$(this).addClass('active');
		var mod = $(this).attr('data-mod');
		var type = $(this).attr('data-type');
		var val = $(this).attr('data-val');
		if(mod == 'content'){
			that.content(type,val);
		}else if(mod == 'mdfiles'){
			that.mdfiles(val);
		}else if(mod == 'publish'){
			that.publish();
		}
	});
	
	$('body').on('click','#maskviewClose',this.maskviewClose);

	$('#newFoldBtn').click(function(){
		var foldname = $('#foldname').val();
		if(foldname != ''){
			$.ajax({
				url:"./ajax.php?mod=addfold&t="+foldname,
				method:"GET",
				dataType:"JSON",
				success:function(rsp){
					if(rsp.code == 200){
						$('#newFold').modal('hide');
						$('#myFolds').click();
					}
				}
			})
		}else{
			alert('文件夹名称不能为空');
		}
	});
	
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