<template>
	<view>
		<!-- 顶部导航 -->
		<fa-navbar :title="label+'记录'" :border-bottom="false"></fa-navbar>		
		<view class="u-p-b-30">
			<view class="bg-white u-p-30 u-flex u-border-bottom" v-for="(item,index) in list" :key="index">
				<u-avatar :src="item.user && item.user.avatar"></u-avatar>
				<view class="u-m-l-15">
					<view class="u-font-35" v-text="item.user && item.user.nickname"></view>
					<view class="u-tips-color">
						{{label}}时间：{{item.createtime}}
					</view>
				</view>
			</view>
		</view>
		<!-- 空数据 -->
		<view class="u-u-p-t-80 u-p-b-80" v-if="!list.length"><u-empty></u-empty></view>
		<!-- 加载更多 -->
		<view class="u-p-b-30" v-if="list.length"><u-loadmore bg-color="#ffffff" :status="has_more ? status : 'nomore'" /></view>
		<!-- 回到顶部 -->
		<u-back-top :scroll-top="scrollTop" :icon-style="{ color: theme.bgColor }" :custom-style="{ backgroundColor: theme.lightColor }"></u-back-top>
		<!-- 底部导航 -->
		<fa-tabbar></fa-tabbar>
	</view>
</template>

<script>
	export default {
		onLoad(e) {
			this.id = e.id || '';
			this.label = e.label || '提交';
			this.getProjectUser()
		},
		data() {
			return {
				id:'',
				label:'提交',
				page:1,
				list:[],
				status: 'loadmore',
				scrollTop: 0,
				has_more:false
			}
		},
		methods: {
			getProjectUser(){
				this.$api.projectUser({id:this.id,page:this.page}).then(res=>{
					if(res.code==1){
						this.list = [...this.list,...res.data.data];
						this.has_more = res.data.current_page < res.data.last_page;
					}
				})
			}
		},
		onReachBottom() {
			if (this.has_more) {
				this.status = 'loading';
				this.page++;
				this.getProjectUser();
			}
		}
	}
</script>

<style>

</style>
