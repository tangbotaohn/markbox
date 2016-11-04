var that;
var vue = new Vue({
	el:'#app',
	data:{
		mdlist:[],
		content:"",
		midtype:"myfiles",
		queryParam:"mdfiles",
		cmd:"content",
		midloading:false,
		contentloading:false
	},
	created:function(){
		that = this;
		this.getMdfiles();
	},
	methods:{
		clickMenu:function(evt){
			var cmd = $(evt.target).attr('c-cmd');
			this.queryParam = $(evt.target).attr('c-val')? $(evt.target).attr('c-val') : '';
			$('#current').html($(evt.target).attr('title'));
			
			$(evt.target).parent().find('.active').removeClass('active');
			$(evt.target).addClass('active');
			switch(cmd){
				case 'mdfiles':
					this.getMdfiles();
					this.cmd = "content";
					if(this.queryParam.indexOf('mdfiles') > -1){
						this.midtype = 'myfiles';
					}else if(this.queryParam.indexOf('publish') > -1){
						this.midtype = 'publishfiles';
					}else if(this.queryParam.indexOf('recycles') > -1){
						this.midtype = 'recyclefiles';
					}else{
						this.midtype = 'myfiles';
					}
					break;
				case 'mdfolds':
					this.getMdfolds();
					this.cmd = "mdfiles";
					if(this.queryParam.indexOf('mdfiles') > -1){
						this.midtype = 'myfolds';
					}else if(this.queryParam.indexOf('publish') > -1){
						this.midtype = 'publishfolds';
					}else{
						this.midtype = 'myfolds';
					}
					break;
				case 'content':
					this.getContent();
					this.cmd = "content";
					break;
			}
			console.log(cmd,this.queryParam,this.midtype)
		},
		getMdfiles:function(){
			that.midloading = true;
			$.ajax({
				url:"./ajax.php?mod=mdfiles&t="+that.queryParam,
				method:"GET",
				dataType:"JSON",
				success:function(rsp){
					if(rsp.code != 200){
						return that.dialog('alert',rsp.data);
					}
					that.mdlist = rsp.data;
					that.listype = "mdfiles";
					that.midloading = false;
				}
			});
		},
		getMdfolds:function(){
			that.midloading = true;
			$.ajax({
				url:"./ajax.php?mod=mdfolds&t="+that.queryParam,
				method:"GET",
				dataType:"JSON",
				success:function(rsp){
					if(rsp.code != 200){
						return that.dialog('alert',rsp.data);
					}
					that.mdlist = rsp.data;
					that.listype = "mdfolds";
					that.midloading = false;
				}
			});
		},
		getContent:function(){
			that.contentloading = true;
			$.ajax({
				url:'./ajax.php?mod=content&t='+that.queryParam,
				method:"GET",
				dataType:'JSON',
				success:function(rsp){
					if(rsp.code != 200){
						return that.dialog('alert',rsp.data);
					}
					that.content = rsp.data.html;
					that.contentloading = false;
				}
			});
		},
		delFile:function(){
			$.ajax({
				url:'./ajax.php?mod=delfile&t='+that.queryParam,
				method:"GET",
				dataType:'JSON',
				success:function(rsp){
					if(rsp.code != 200){
						return that.dialog('alert',rsp.data);
					}
					that.content = '';
					for(var i in that.mdlist){
						if(that.mdlist[i].file == that.queryParam){
							that.mdlist.$remove(that.mdlist[i]);
						}
					}
				}
			});
		},
		mvtorecycle:function(){
			$.ajax({
				url:'./ajax.php?mod=move&t='+that.queryParam+'&mv=recycles',
				method:"GET",
				dataType:'JSON',
				success:function(rsp){
					if(rsp.code != 200){
						return that.dialog('alert',rsp.data);
					}
					that.content = '';
					for(var i in that.mdlist){
						if(that.mdlist[i].file == that.queryParam){
							that.mdlist.$remove(that.mdlist[i]);
						}
					}
				}
			});
		},
		delFold:function(evt){
			var fold = $(evt.target).attr('c-val');
			if(!fold) return;
			$.ajax({
				url:'./ajax.php?mod=delfold&t='+fold,
				method:"GET",
				dataType:'JSON',
				success:function(rsp){
					if(rsp.code != 200){
						return that.dialog('alert',rsp.data);
					}
					that.content = '';
					for(var i in that.mdlist){
						if(that.mdlist[i].file == fold){
							that.mdlist.$remove(that.mdlist[i]);
						}
					}
				}
			});
		},
		dialog:function(name,msg){
			$.ajax({
				url:'./themes/dialogs/'+name+'.html',
				method:"GET",
				success:function(html){
					var ele = $(html);
					if(msg) ele.find('#alertMsg').html(msg);
					$('body').append(ele);
				}
			})
		}
	}
});

function closeDialog(){
	$('#MarkDialog').remove();
}
