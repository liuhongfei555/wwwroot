<template>
	<view>
		<view class="u-p-30 u-border-bottom u-flex u-col-center">
			<view class="line" :style="[{ backgroundColor: theme.bgColor }]"></view>
			<view class="u-item-title" v-text="item.title"></view>
		</view>
		<view class="content u-p-30 u-flex" v-if="item.type == 'files'">
			<u-grid :col="4" :border="true">
				<u-grid-item v-for="(res, ink) in item.value" :key="ink" @click="click(res)">
					<u-icon name="download" :size="46"></u-icon>
					<view class="grid-text">{{ fileType(res) }}</view>
				</u-grid-item>
			</u-grid>
		</view>
		<view class="content u-p-30 u-flex" v-else>
			<u-grid :col="4" :border="true">
				<u-grid-item @click="click(item.value)">
					<u-icon name="download" :size="46"></u-icon>
					<view class="grid-text">{{ fileType(item.value) }}</view>
				</u-grid-item>
			</u-grid>
		</view>
		<view class="u-p-30" v-if="downtips">
			<u-alert-tips type="warning" title="下载成功,保存路径为:" :close-able="true" :show="downtips" :description="description" @close="downtips = false"></u-alert-tips>
		</view>
	</view>
</template>

<script>
export default {
	props: {
		item: {
			type: Object,
			default: {}
		}
	},
	computed: {
		fileType() {
			return filename => {
				var index1 = filename.lastIndexOf('.');
				var index2 = filename.length;
				return filename.substring(index1, index2);
			};
		}
	},
	data() {
		return {
			downtips: false,
			description: ''
		};
	},
	methods: {
		click(url) {
			let that = this;
			// #ifndef H5
			uni.downloadFile({
				url: url,
				success: res => {
					if (res.statusCode === 200) {
						uni.saveFile({
							tempFilePath: res.tempFilePath,
							success: function(res) {
								that.downtips = true;
								that.description = res.savedFilePath;
								console.log(res);
							},
							fail(err) {
								console.log(err);
							}
						});
					}
				},
				fail: function(e) {
					console.log(e);
				}
			});
			// #endif
			// #ifdef H5
			window.open(url);
			// #endif
		}
	}
};
</script>

<style lang="scss"></style>
