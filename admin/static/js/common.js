var that;
var vue = new Vue({
	el:'#app',
	data:{
		mdlist:[],
		content:"",
		queryParam:"mdfiles",
		cmod:"content",
		readdir:"mdfiles",
	},
	created:function(){
		that = this;
		this.getMdfiles();
	},
	methods:{
		clickMenu:function(evt){
			var mod = $(evt.target).attr('c-mod');
			this.queryParam = $(evt.target).attr('c-val')? $(evt.target).attr('c-val') : '';
			$('#current').html($(evt.target).attr('title'));
			console.log(mod,this.queryParam,this.readdir)
			$(evt.target).parent().find('.active').removeClass('active');
			$(evt.target).addClass('active');
			switch(mod){
				case 'mdfiles':
					this.getMdfiles();
					this.cmod = "content";
					this.readdir = 'mdfiles';
					break;
				case 'mdfolds':
					this.getMdfolds();
					this.cmod = "mdfiles";
					this.readdir = 'mdfiles';
					break;
				case 'publish':
					this.getPublish();
					this.cmod = "content";
					this.readdir = 'publish';
					break;
				case 'recycles':
					this.getRecycles();
					this.cmod = "content";
					this.readdir = 'recycles';
					break;
				case 'content':
					this.getContent();
					this.cmod = "content";
					break;
			}
		},
		getMdfiles:function(){
			$.ajax({
				url:"./ajax.php?mod=mdfiles&t="+that.queryParam,
				method:"GET",
				dataType:"JSON",
				success:function(rsp){
					that.mdlist = rsp.data;
					that.listype = "mdfiles";
				}
			});
		},
		getMdfolds:function(){
			$.ajax({
				url:"./ajax.php?mod=mdfolds&t="+that.queryParam,
				method:"GET",
				dataType:"JSON",
				success:function(rsp){
					that.mdlist = rsp.data;
					that.listype = "mdfolds";
				}
			});
		},
		getPublish:function(){
			$.ajax({
				url:"./ajax.php?mod=publish&t="+that.readdir,
				method:"GET",
				dataType:"JSON",
				success:function(rsp){
					that.mdlist = rsp.data;
					that.listype = "publish";
				}
			});
		},
		getRecycles:function(){
			$.ajax({
				url:"./ajax.php?mod=recycles&t="+that.readdir,
				method:"GET",
				dataType:"JSON",
				success:function(rsp){
					that.mdlist = rsp.data;
					that.listype = "recycles";
				}
			});
		},
		getContent:function(){
			$.ajax({
				url:'./ajax.php?mod=content&t='+that.queryParam,
				method:"GET",
				dataType:'JSON',
				success:function(rsp){
					that.content = rsp.data;
				}
			});
		},
		delFile:function(){
			$.ajax({
				url:'./ajax.php?mod=delfile&t='+that.queryParam,
				method:"GET",
				dataType:'JSON',
				success:function(rsp){
					that.content = '';
					document.querySelector('#firstMenu').querySelector('.active').click();
				}
			});
		},
		dialog:function(name){
			$.ajax({
				url:'./themes/dialogs/'+name+'.html',
				method:"GET",
				success:function(html){
					var ele = $(html);
					ele.attr('data-path',that.queryParam);
					$('body').append(ele);
				}
			})
		}
	}
});

function closeDialog(){
	$('#MarkDialog').remove();
}
