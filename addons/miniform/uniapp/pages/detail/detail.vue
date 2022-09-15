<template>
	<view class="u-skeleton">
		<!-- 顶部导航 -->
		<fa-navbar title="详情" :border-bottom="false"></fa-navbar>
		<view class="bg-white u-skeleton-fillet">
			<u-swiper bg-color="#fffff" border-radius="0" :list="detail.images_text" height="350" @click="swipers"></u-swiper>
		</view>
		<view class="u-p-l-30 u-p-r-30 u-p-t-30 bg-white">
			<view class="title u-p-b-30 u-border-bottom u-skeleton-rect">{{ detail.title }}</view>

			<view class="u-p-t-5 u-tips-color u-flex u-row-between">
				<view class="u-skeleton-rect">
					<view class="" v-if="detail.price > 0">
						费用：
						<text class="price">￥{{ detail.price }}</text>
					</view>
					<view class="" v-else><text class="price">免费</text></view>
				</view>
				<view class="u-flex u-skeleton-rect">
					<view class="u-m-r-15">
						<u-icon name="eye-fill"></u-icon>
						<text class="u-m-l-5">浏览:</text>
						<text class="u-m-l-5">{{detail.views}}</text>
						<text class="u-m-l-20">|</text>
					</view>
					<view class="u-flex" v-if="detail.people_num <= 0">
						<text class="" @click="goPage('/pages/record/record?id=' + id + '&label=' + (detail.label || ''))" v-text="'已' + (detail.label || '提交') + ':' + (detail.registered ? detail.registered : '0') + '人'"></text>
						<text class="u-m-l-20">|</text>
					</view>
					<view class="u-flex u-m-l-20" @click="showShare=true">
						分享
						<button type="default" class="cu-btn">
							<u-icon name="share" color="#909399"></u-icon>
						</button>
					</view>
				</view>
			</view>
		</view>
		<!-- 进度+倒计时 -->
		<view class="u-p-t-30 u-p-l-30 u-p-r-30" v-if="detail.people_num > 0 || detail.surplus_time > 0">
			<view class="" :style="[{ backgroundColor: theme.bgColor, borderRadius: '8rpx' }]">
				<view class="u-flex u-row-between u-p-30" v-if="detail.surplus_time > 0">
					<view class="" :style="[{ color: theme.color }]">距离活动{{ detail.status_ing=='未开始'?'开始':'结束'}}还剩</view>
					<u-count-down :timestamp="detail.surplus_time" :show-days="true" :show-hours="true" separator="zh" :color="theme.bgColor" separator-color="#fff" @end="countdownEnd"></u-count-down>
				</view>
				<view class="u-p-l-30 u-p-r-30 u-p-b-30" :class="{ 'u-p-t-30': detail.surplus_time <= 0 }" v-if="detail.people_num > 0" @click="goPage('/pages/record/record?id=' + id + '&label=' + (detail.label || ''))">
					<view class="">
						<u-line-progress active-color="#ff9900" :striped="true" :striped-active="true" :percent="(detail.registered / detail.people_num) * 100"></u-line-progress>
					</view>
					<view class="u-flex u-row-between u-font-20 u-m-t-10 u-p-l-5 u-p-r-5" :style="[{ color: theme.color }]">
						<view class="">已报名:{{ detail.registered }}</view>
						<view class="">名额:{{ detail.people_num }}</view>
					</view>
				</view>
			</view>
		</view>

		<view class="u-m-t-30 bg-white u-skeleton-rect" v-if="detail.front_back != 1">
			<view class="u-border-bottom u-p-30 u-flex u-col-center u-row-center">
				<view class="lines line-left"></view>
				<view class="u-m-l-50 u-m-r-50">详情</view>
				<view class="lines line-right"></view>
			</view>
			<view class="u-p-30 u-skeleton-rect">
				<u-parse :html="detail.content" :tag-style="vuex_parse_style" :domain="vuex_config.upload ? vuex_config.upload.cdnurl : ''" @linkpress="navigate"></u-parse>
			</view>
		</view>

		<view class="u-m-t-30 u-skeleton-rect">
			<block v-for="(item, index) in fields" :key="index">
				<!-- 地点 -->
				<view class="item" v-if="item.type == 'city'">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<u-icon name="map"></u-icon>
						<text class="u-m-l-10" :selectable="true">{{ item.value }}</text>
					</view>
				</view>

				<!-- 定位 -->
				<view class="item" v-if="item.type == 'location'">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex" @click="lookMap(item.value)">
						<u-icon name="map"></u-icon>
						<text class="u-m-l-10" :selectable="true">{{ item.value && item.value.address }}</text>
					</view>
				</view>
				<!-- 文本,数字 -->
				<view class="item" v-if="['string', 'text', 'number', 'selectpage'].indexOf(item.type) != -1">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<text class="u-m-l-10" :selectable="true">{{ item.value }}</text>
					</view>
				</view>

				<!-- 文件下载 -->
				<view class="item" v-if="['files', 'file'].indexOf(item.type) != -1">
					<fa-download :item="item"></fa-download>
				</view>

				<!-- 列表 -->
				<view class="item" v-if="item.type == 'select' || item.type == 'radio'">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<text class="u-m-l-10" :selectable="true">{{ item.content_list[item.value] }}</text>
					</view>
				</view>
				<!-- 开关 -->
				<view class="item" v-if="item.type == 'switch'">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<text class="u-m-l-10">{{ item.value }}</text>
					</view>
				</view>

				<!-- 关联多选 -->
				<view class="item" v-if="item.type == 'selectpages'">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<text class="u-m-l-10" :selectable="true">{{ item.value.join(',') }}</text>
					</view>
				</view>

				<!-- 列表多选 -->
				<view class="item" v-if="item.type == 'selects' || item.type == 'checkbox'">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<text class="u-m-l-10">{{ listSelects(item) }}</text>
					</view>
				</view>
				<!-- 图 -->
				<view class="item" v-if="['images', 'image'].indexOf(item.type) != -1">
					<fa-images :item="item"></fa-images>
				</view>
				<!-- 富文本 -->
				<view class="item" v-if="item.type == 'editor'">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<u-parse :html="item.value" :tag-style="vuex_parse_style" :domain="vuex_config.upload ? vuex_config.upload.cdnurl : ''" @linkpress="navigate"></u-parse>
					</view>
				</view>
				<!-- 时间 -->
				<view class="item" v-if="['date', 'time', 'datetime', 'datetimerange'].indexOf(item.type) != -1">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<u-icon name="clock"></u-icon>
						<text class="u-m-l-10" :selectable="true" v-text="item.value"></text>
					</view>
				</view>

				<!-- 数组 -->
				<view class="item" v-if="item.type == 'array'">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30">
						<u-table>
							<u-tr class="u-tr">
								<u-th class="u-th" v-text="item.setting.key"></u-th>
								<u-th class="u-th" v-text="item.setting.value"></u-th>
							</u-tr>
							<u-tr class="u-tr" v-for="(res, ink) in item.value" :key="ink">
								<u-td class="u-td" v-text="ink"></u-td>
								<u-td class="u-td" v-text="res"></u-td>
							</u-tr>
						</u-table>
					</view>
				</view>
			</block>
		</view>

		<view class="u-m-t-30 bg-white u-skeleton-rect" v-if="detail.front_back == 1">
			<view class="u-border-bottom u-p-30 u-flex u-col-center u-row-center">
				<view class="lines line-left"></view>
				<view class="u-m-l-50 u-m-r-50">详情</view>
				<view class="lines line-right"></view>
			</view>
			<view class="u-p-30">
				<u-parse :html="detail.content" :tag-style="vuex_parse_style" :domain="vuex_config.upload ? vuex_config.upload.cdnurl : ''" @linkpress="navigate"></u-parse>
			</view>
		</view>

		<u-gap height="145" bg-color="#f4f6f8"></u-gap>

		<view class="bg-white foot u-skeleton-rect">
			<u-button type="default" hover-class="none" :custom-style="customStyle" shape="circle" @click="goToSubmit" :disabled="detail.status_ing == '未开始' || detail.status_ing == '已结束'">
				{{ detail.button_text }}
			</u-button>
		</view>
		<u-skeleton :loading="loading" :animation="true" bgColor="#FFF"></u-skeleton>
		<fa-share v-model="showShare" :title="detail.title" :summary="detail.title" @shares="showPoster = true"></fa-share>
		<fa-poster v-model="showPoster" :info="detail"></fa-poster>
		<!-- 底部导航 -->
		<fa-tabbar></fa-tabbar>
	</view>
</template>

<script>
	// #ifdef H5
	import { weixinShare } from '@/common/fa.weixin.mixin.js';
	// #endif
	import { tools } from '@/common/fa.mixin.js';
	export default {
		mixins: [
			tools,
			// #ifdef H5
			weixinShare
			// #endif
		],
		computed: {
			listSelects() {
				return item => {
					if (!item) {
						return item;
					}
					let arr = [];
					let varArr = item.value.split(',');
					varArr.forEach(res => {
						arr.push(item.content_list[res]);
					});
					return arr.join(',');
				};
			},
			fileType() {
				return filename => {
					var index1 = filename.lastIndexOf('.');
					var index2 = filename.length;
					return filename.substring(index1, index2);
				};
			},
			customStyle() {
				if (this.detail.status_ing == '已结束') {
					// #ifdef MP-ALIPAY
					return { color: '#ccc' }
					// #endif
					// #ifndef MP-ALIPAY
					return {}
					// #endif
				} else {
					return { backgroundColor: this.theme.bgColor, color: this.theme.color };
				}


			},
			endtime() {
				return time => {
					return time - new Date().getTime() / 1000;
				};
			}
		},
		onLoad(e) {
			this.id = e.id || '';
			let invite_id = e.invite_id || '';
			if (e.scene) {
				const scene = decodeURIComponent(e.scene);
				let project_id = this.$util.getQueryString('project_id', scene);
				if (project_id) {
					this.id = project_id;
				}
				invite_id = this.$util.getQueryString('invite_id', scene) || invite_id;
			}
			if (invite_id) {
				this.$u.vuex('vuex_invite_id', user_id);
			}
			this.projectInfo();
		},
		data() {
			return {
				fields: [],
				detail: {},
				id: '',
				loading: true, // 是否显示骨架屏组件
				showShare: false,
				showPoster: false
			};
		},
		methods: {
			lookMap(item) {
				if (!item) return;
				uni.openLocation({
					latitude: parseFloat(item.lat),
					longitude: parseFloat(item.lng),
					success: function() {
						console.log('success');
					}
				});
			},
			swipers(index) {
				uni.previewImage({
					current: index,
					urls: this.detail.images_text,
					longPressActions: {
						itemList: ['发送给朋友', '保存图片', '收藏'],
						success: function(data) {
							console.log(data);
						},
						fail: function(err) {
							console.log(err.errMsg);
						}
					}
				});
			},
			countdownEnd() {
				this.projectInfo();
			},
			goToSubmit() {
				if (this.detail.is_need_login && !this.vuex_token) {
					this.$u.route('/pages/login/mobilelogin');
				} else {
					this.$u.route({
						type: 'navigateTo',
						url: '/pages/detail/diyform',
						params: { id: this.id }
					});
				}
			},
			projectInfo() {
				this.$api.projectInfo({ id: this.id }).then(res => {
					if (res.code == 1) {
						this.fields = res.data.fields;
						this.detail = res.data.info;
						this.loading = false;
						// #ifdef MP-WEIXIN
						this.$u.mpShare = {
							title: this.detail.title, // 默认为小程序名称，可自定义
							path: '', // 默认为当前页面路径，一般无需修改，QQ小程序不支持
							imageUrl: this.detail.images_text.length > 0 ? this.detail.images_text[0] : ''
						};
						// #endif
						// #ifdef H5
						if (this.$util.isWeiXinBrowser()) {
							this.wxShare({
								title: this.detail.title,
								desc: this.detail.content.replace(/<\/?.+?\/?>/g, ''),
								link: window.location.href,
								img: this.detail.images_text.length > 0 ? this.detail.images_text[0] : ''
							});
						}
						// #endif
					}
				});
			}
		}
	};
</script>

<style lang="scss">
	page {
		background-color: #f4f6f8;
	}
</style>
<style lang="scss" scoped>
	.thumb {
		width: 100vw;
		height: 360rpx;
	}

	.title {
		font-size: 38rpx;
	}

	.u-item-title {
		position: relative;
		font-size: 30rpx;
		padding-left: 16rpx;
		line-height: 1;
	}

	.lines {
		width: 150rpx;
		height: 3rpx;

		&.line-left {
			background-image: linear-gradient(90deg, #eee, #f0f2f3);
		}

		&.line-right {
			background-image: linear-gradient(90deg, #f0f2f3, #eee);
		}
	}

	.line {
		width: 8rpx;
		height: 32rpx;
		border-radius: 10rpx;
	}

	.item {
		margin-bottom: 30rpx;
		background-color: #ffffff;

		.content {
			// padding: 20rpx 10rpx;
		}
	}

	.foot {
		position: fixed;
		bottom: 0;
		left: 0;
		width: 100vw;
		background-color: #ffffff;
		padding: 30rpx 80rpx;
		border-top: 1px solid #f4f6f8;
		z-index: 100;
	}

	.cu-btn {
		background-color: #ffffff;
		padding: 0 10rpx;
		border: none;

		&::after {
			border: none;
		}
	}
</style>
