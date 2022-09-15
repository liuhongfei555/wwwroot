<template>
	<view>
		<!-- 顶部导航 -->
		<fa-navbar title="活动报名" :border-bottom="false"></fa-navbar>
		<view class="">
			<view class="u-padding-15" :style="[{ background: theme.bgColor }]"><u-search placeholder="关键词搜索" :show-action="false" v-model="keyword"></u-search></view>
			<u-tabs
				:list="tabList"
				:is-scroll="true"
				:bg-color="theme.bgColor"
				:active-color="theme.color"
				:bar-width="tabwidth"
				inactive-color="#eee"
				:current="current"
				@change="change"
			></u-tabs>
		</view>
		<view class="u-m-t-30"><u-swiper bg-color="#fffff" :list="swiperList" height="350" :title="true" :effect3d="true" @click="openPage"></u-swiper></view>
		<view class="u-p-30 u-m-t-10 u-border-bottom u-flex u-row-between" @click="type = !type">
			<view class="u-flex">
				<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
				<text class="u-m-l-15">项目列表</text>
			</view>
			<u-icon size="30" :name="!type ? 'list' : 'order'"></u-icon>
		</view>
		<view class="list" v-if="!type">
			<view class="item u-flex u-p-30" v-for="(item, index) in list" :key="index" @click="goPage('/pages/detail/detail?id=' + item.id)">
				<image class="thumb" :src="item.image" mode="aspectFill"></image>
				<view class="u-flex-1 u-p-l-30">
					<view class="title u-line-2 u-font-16" v-text="item.title"></view>
					<view class="u-p-t-10 u-flex">
						<view class="">
							<u-tag
								:text="item.status_ing"
								size="mini"
								:bg-color="theme.lightColor"
								:border-color="theme.faBorderColor"
								:color="theme.bgColor"
								shape="circle"
								mode="light"
							/>
						</view>
						<view class="u-m-l-15">
							<u-tag
								:text="'已有 ' + item.registered + ' 人参与'"
								size="mini"
								:bg-color="theme.lightColor"
								:border-color="theme.faBorderColor"
								:color="theme.bgColor"
								shape="circle"
								mode="light"
							/>
						</view>
					</view>
					<view class="u-flex u-p-t-10 u-row-between">
						<view :class="item.price > 0 ? 'price' : 'free'">
							<text>{{ item.price > 0 ? '￥' + item.price : '免费' }}</text>
						</view>
						<view class="u-tips-color">
							<u-icon name="account-fill"></u-icon>
							<text class="u-m-l-10">{{ item.people_num > 0 ? item.people_num : '不限' }}</text>
						</view>
					</view>
					<view class="u-p-t-10 u-tips-color u-font-24">
						<u-icon name="clock"></u-icon>
						<text class="u-m-l-10" v-text="item.createtime_text"></text>
					</view>
				</view>
			</view>
		</view>
		<view class="u-p-l-30 u-p-r-30 u-p-t-30 grid" v-if="type">
			<view class="item u-m-b-30" v-for="(item, index) in list" :key="index" @click="goPage('/pages/detail/detail?id=' + item.id)">
				<fa-swiper :list="item.images_text | imagesText" :height="350" :autoplay="false" mode="none" :params="{id:item.id}" @click="swiperItem"></fa-swiper>
				<view class="u-p-t-15 u-p-b-30">
					<view class="title u-line-2 u-font-16"><text v-text="item.title"></text></view>
					<view class="u-p-t-15 u-flex">
						<view class="">
							<u-tag
								:text="item.status_ing"
								size="mini"
								:bg-color="theme.lightColor"
								:border-color="theme.faBorderColor"
								:color="theme.bgColor"
								shape="circle"
								mode="light"
							/>
						</view>
						<view class="u-m-l-15">
							<u-tag
								:text="'已有 ' + item.registered + ' 人参与'"
								size="mini"
								:bg-color="theme.lightColor"
								:border-color="theme.faBorderColor"
								:color="theme.bgColor"
								shape="circle"
								mode="light"
							/>
						</view>
					</view>
					<view class="u-flex u-p-t-15 u-row-between">
						<view :class="item.price > 0 ? 'price' : 'free'">
							<text>{{ item.price > 0 ? '￥' + item.price : '免费' }}</text>
						</view>
						<view class="u-tips-color">
							<u-icon name="account-fill"></u-icon>
							<text class="u-m-l-10">{{ item.people_num > 0 ? item.people_num : '不限' }}</text>
						</view>
					</view>
					<view class="u-p-t-15 u-tips-color u-font-24">
						<u-icon name="clock"></u-icon>
						<text class="u-m-l-10" v-text="item.createtime_text"></text>
					</view>
				</view>
			</view>
		</view>
		<!-- 空数据 -->
		<view class="u-u-p-t-80 u-m-t-80 u-p-b-80" v-if="!list.length"><u-empty text="暂时没有更多的项目..."></u-empty></view>
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
	data() {
		return {
			keyword: '',
			type: false,
			current: 0,
			tabwidth: 40,
			status: 'loadmore',
			has_more: false,
			scrollTop: 0,
			list: [],
			page: 1,
			category_id: '',
			is_update: false
		};
	},
	computed: {
		tabList() {
			return [{ name: '全部', id: '' }, ...(this.vuex_config.category || [])];
		},
		swiperList() {
			return this.vuex_config.swiper || [];
		}
	},
	watch: {
		keyword(newValue, oldValue) {
			this.is_update = true;
			this.project();
		}
	},
	onLoad() {
		this.project();
		this.index_list()
	},
	methods: {
		index_list(){
			setTimeout(()=>{
				if(typeof this.vuex_config.index_list != 'undefined'){
					this.type = this.vuex_config.index_list!=1;
				}else{
					this.index_list();
				}
			},100)
		},
		swiperItem(e,item){
			this.goPage('/pages/detail/detail?id=' + item.id)
		},
		change(index) {
			//重设Bar宽度
			this.tabwidth = this.$util.strlen(this.tabList[index].name) * 30;
			this.current = index;
			this.page = 1;
			this.category_id = this.tabList[index].id;
			this.is_update = true;
			this.project();
		},
		openPage(index) {
			let path = this.vuex_config.swiper[index].path;
			if (path == '/' || !path) {
				return;
			}
			if (path.substr(0, 1) == 'p') {
				path = '/' + path;
			}
			if (path.indexOf('http') != -1) {
				this.$u.vuex('vuex_webs', this.vuex_config.swiper[index]);
				this.$u.route('/pages/webview/webview');
				return;
			}
			this.$u.route({
				url: path
			});
		},
		project() {
			this.$api.project({ keyword: this.keyword, page: this.page, category_id: this.category_id }).then(res => {
				uni.stopPullDownRefresh();
				if (res.code) {
					if (this.is_update) {
						this.list = [];
						this.is_update = false;
					}
					this.list = [...this.list, ...res.data.data];
					this.has_more = res.data.current_page < res.data.last_page;
				}
			});
		}
	},
	filters:{
		imagesText(item){
			let list = [];
			item.map(val=>{
				list.push({image:val});
			})
			return list;
		}
	},
	onPageScroll(e) {
		this.scrollTop = e.scrollTop;
	},
	//下拉刷新
	onPullDownRefresh() {
		this.is_update = true;
		this.page = 1;
		this.project();
	},
	onReachBottom() {
		if (this.has_more) {
			this.status = 'loading';
			this.page++;
			this.project();
		}
	}
};
</script>
<style lang="scss">
page {
	background-color: #ffffff;
}
</style>
<style lang="scss" scoped>
.line {
	width: 8rpx;
	height: 32rpx;
	border-radius: 10rpx;
}
.list {
	padding-bottom: 30rpx;
	.item {
		border-bottom: 1px solid #f4f6f8;
		.thumb {
			width: 250rpx;
			height: 200rpx;
			border-radius: 10rpx;
		}
		.title {
			// font-weight: bold;
		}

		.free {
			color:$u-type-success-dark;
		}
	}
}
.grid {
	.item {
		border-bottom: 1px solid #f4f6f8;
		.thumb {
			width: 100%;
			height: 350rpx;
			border-radius: 10rpx;
		}
		.title {
			// font-weight: bold;
		}

		.free {
			color:$u-type-success-dark;
		}
	}
}
</style>
