<template>
	<view>
		<!-- 顶部导航 -->
		<fa-navbar :title="project.title || '项目详情'" :border-bottom="false"></fa-navbar>
		<view class="u-m-t-30">
			<block v-for="(item, index) in forminfo" :key="index">
				<!-- 地点 -->
				<view class="item" v-if="item.type == 'city'">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<u-icon name="map"></u-icon>
						<text class="u-m-l-10">{{ item.value }}</text>
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
						<text class="u-m-l-10">{{ item.value && item.value.address }}</text>
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
				<view class="item" v-if="['files', 'file'].indexOf(item.type) != -1"><fa-download :item="item"></fa-download></view>

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
						<text class="u-m-l-10" :selectable="true">{{ listSelects(item) }}</text>
					</view>
				</view>
				<!-- 图 -->
				<view class="item" v-if="['images', 'image'].indexOf(item.type) != -1"><fa-images :item="item"></fa-images></view>
				<!-- 富文本 -->
				<view class="item" v-if="item.type == 'editor'">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<u-parse
							:html="item.value"
							:tag-style="vuex_parse_style"
							:domain="vuex_config.upload ? vuex_config.upload.cdnurl : ''"
							@linkpress="navigate"
						></u-parse>
					</view>
				</view>
				<!-- 时间 -->
				<view class="item" v-if="['date', 'time', 'datetime', 'datetimerange'].indexOf(item.type) != -1">
					<view class="u-p-30 u-border-bottom u-flex u-col-center">
						<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
						<view class="u-item-title" v-text="item.title"></view>
					</view>
					<view class="content u-p-30 u-flex">
						<u-icon name="calendar" v-if="item.type == 'datetimerange'"></u-icon>
						<u-icon name="clock" v-else></u-icon>
						<text class="u-m-l-10" :selectable="true" v-text="item.value || (item.name == 'signin_time' ? '未签到' : '')"></text>
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

			<view class="item" v-if="project.is_verification && project.logs_status=='normal'">
				<view class="u-p-30 u-border-bottom u-flex u-col-center">
					<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
					<view class="u-item-title">核销码</view>
				</view>
				<view class="u-flex">
					<view class="u-padding-30" v-if="!project.verification_status">
						<view style="width: 300rpx;height: 300rpx;">
							<tki-qrcode
								ref="qrcode"
								:val="project.verification_text"
								:size="300"
								background="#ffffff"
								foreground="#000000"
								pdground="#000000"
								@result="qrR"
							></tki-qrcode>
						</view>
						<view class="u-m-t-20">{{ project.verification_text }}</view>
					</view>
					<view class="u-padding-30" v-else>
						<text class="u-type-success">已核销！</text>
					</view>
				</view>
			</view>

		</view>
		<!-- 底部导航 -->
		<fa-tabbar></fa-tabbar>
	</view>
</template>

<script>
import { tools } from '@/common/fa.mixin.js';
import tkiQrcode from '@/components/tki-qrcode/tki-qrcode.vue';

export default {
	components: {
		tkiQrcode
	},
	mixins: [tools],
	onLoad(e) {
		this.id = e.id || '';
		this.logDetail();
	},
	computed: {
		listSelects() {
			return item => {
				let arr = [];
				let varArr = item.value.split(',');
				varArr.forEach(res => {
					arr.push(item.content_list[res]);
				});
				return arr.join(',');
			};
		}
	},
	data() {
		return {
			id: '',
			project: {},
			forminfo: []
		};
	},
	methods: {
		logDetail() {
			this.$api.logDetail({ id: this.id }).then(res => {
				if (res.code) {
					this.forminfo = res.data.forminfo;
					this.project = res.data.project;
					if(this.project.is_verification && this.project.logs_status=='normal' && !this.project.verification_status){
						let that = this;
						this.$nextTick(function(){
							that.$refs.qrcode._makeCode();
						})
					}
				}
			});
		},
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
		qrR(r) {
			// console.log(r)
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
.item {
	margin-bottom: 30rpx;
	background-color: #ffffff;
	.content {
		// padding: 20rpx 10rpx;
	}
}
</style>
