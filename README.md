# Gridcraft Portfolio

网格作品集 WordPress 主题。为设计师、摄影师、独立开发者打造：瀑布流网格、原生分类筛选、手写 Lightbox、作品详情页模板，首页含 Hero 区 + 精选作品 + 服务介绍 + 客户 + 约稿 CTA。

- 文本域：`gridcraft-portfolio`
- 版本：1.0.0
- 需要 WordPress：6.0 及以上
- 需要 PHP：7.4 及以上
- 测试到：WordPress 6.6
- 许可证：MIT

---

## 简介

建作品集站最容易踩的两个坑：

**一是作品数据结构没设计好。** 「客户」和「技术栈」这类信息塞进正文，筛选和展示都很别扭。Gridcraft 把它们做成了独立的数据层：两个自定义文章类型（`portfolio` 作品、`client` 客户）加一组 Meta 框（项目链接、客户、年份、角色、技术栈），全部注册到 REST API，区块编辑器里也能读写。

**二是筛选交互需要引一堆库。** Gridcraft 的分类筛选用原生 `fetch` 打 `admin-ajax.php`，Lightbox 是手写的 200 行 JS（含键盘导航、焦点陷阱、滚动锁定）。整个主题零外部依赖，不加载任何 CDN 资源。

## 特性

### 内容类型

| 类型 | 用途 | 关键能力 |
| --- | --- | --- |
| `portfolio`（作品） | 作品集条目 | 缩略图、分类法、Meta 框、评论、页面属性（排序） |
| `client`（客户） | 客户 / 品牌列表 | Logo、官网地址、所属行业 |

分类法：

- `portfolio_category` — 作品分类（层级式，对应作品「门类」）
- `portfolio_tag` — 作品标签（扁平式，对应技术 / 行业标签）

**作品的 Meta 字段：**

| 字段 | 类型 | 说明 |
| --- | --- | --- |
| 项目链接 | URL | 显示为「访问项目」按钮 |
| 客户 | 下拉选择 | 关联到 `client` 条目 |
| 年份 | 文本 | 归档筛选与排序用 |
| 承担角色 | 文本 | 英文逗号分隔 |
| 技术栈 | 多行文本 | 每行一项 |
| 精选 | 复选框 | 控制是否出现在首页精选区块 |

所有 Meta 字段都做了 Nonce 校验、`current_user_can()` 权限校验、按类型分别清洗（`esc_url_raw` / `absint` / `sanitize_textarea_field` / `sanitize_text_field`），并通过 `register_post_meta()` 注册到 REST API。

### 展示

- **瀑布流网格**：CSS `column-count` 实现，不依赖 JS 计算高度。桌面端 2 / 3 / 4 列可配，900px 以下降为 2 列，640px 以下为 1 列。
- **Hover 动效**：图片轻微放大（`scale(1.03)`）+ 渐变遮罩淡入，显示分类、年份与标题。
- **分类筛选条**：原生 `fetch` 请求 `admin-ajax.php`，带 Nonce 校验。结果数量不超过已渲染数量时走客户端瞬时筛选，否则服务端按分类分页。请求失败自动降级为分类归档链接。
- **Lightbox**：正文大图可点击放大，支持 `Esc` 关闭、`←` `→` 翻页、焦点陷阱、body 滚动锁定。零依赖手写实现。
- **作品详情页**（`single-portfolio.php`）：信息卡片（年份 / 客户 / 角色 / 技术栈 / 分类）+ 访问项目按钮 + 技术栈标签 + 上一个 / 下一个作品 + 同分类相关作品。
- **首页区块**：Hero（支持背景图 + 暗色蒙层）→ 精选作品 → 数据统计条 → 服务介绍 → 客户墙 → 约稿 CTA。每个区块可独立开关。
- 服务列表与统计条都提供 `gridcraft_services` / 过滤器扩展点；统计条的作品数、客户数、年份跨度是从真实数据算出来的，不是硬编码。

### 工程细节

- 完整模板层级，作品与客户有专属归档模板。
- 所有内联图标为固定 SVG 字面量表（`gridcraft_inline_icon()`），无图标字体。
- `wp_kses_allowed_html` 补充 SVG 标签白名单，保证前台输出不被误过滤。
- PHP 7.4 兼容，未使用 PHP 8 独有语法。

## 截图说明

WordPress 后台的主题截图要求尺寸为 **1200 × 900 像素**（PNG 格式）。

- **文件路径**：`screenshot.png`（放在主题根目录）
- **推荐内容**：以首页 Hero 为主视角。上方是深色 Hero 区，橙红色大标题与两个按钮，底部有向下滚动提示；Hero 之下露出精选作品网格的前两行（三列瀑布流，鼠标悬停其中一张显示渐变遮罩）。左上角是透明叠加的站点头部。
- **建议分辨率**：1200 × 900 px，24 位色 PNG，文件体积控制在 250KB 以内。

> 本仓库为纯代码分发，未附带二进制截图文件；安装后请按上述说明自行补充 `screenshot.png`。

## 安装

### 从后台安装（推荐）

1. 把主题目录打包成 `gridcraft-portfolio.zip`，确保压缩包内层是 `gridcraft-portfolio/` 目录。
2. 进入 **外观 → 主题 → 添加新主题 → 上传主题**。
3. 点击 **启用**。主题会在 `after_switch_theme` 时自动刷新固定链接，作品页 URL 立即可用。

### 通过 FTP / SSH 上传

1. 将 `gridcraft-portfolio` 目录上传到 `wp-content/themes/`。
2. 进入 **外观 → 主题**，点击 **启用**。

### 本地开发

```bash
git clone https://github.com/mornrain/gridcraft-portfolio.git
cd gridcraft-portfolio
php -l functions.php   # 语法自检
```

## 主题配置项

进入 **外观 → 自定义 → 主题设置**。

### 配色

| 配置项 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| 强调色 | 颜色选择器 | `#ff5a36` | 留空使用主题默认橙红色 |
| 页面底色 | 颜色选择器 | `#ffffff` | 覆盖站点底色 |

### 首页 Hero

| 配置项 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| 眉标 | 文本 | `独立设计师 / 开发者` | Hero 顶部的小胶囊标签 |
| 主标题 | 文本 | `把想法做成看得见的作品` | |
| 副文案 | 多行文本 | 内置默认文案 | |
| 主按钮文案 | 文本 | `查看作品` | 留空时链接指向作品归档页 |
| 主按钮链接 | URL | 作品归档页 | |
| 次按钮文案 | 文本 | `聊聊合作` | 留空时按钮不输出 |
| 次按钮链接 | URL | 无 | 不填则隐藏次按钮 |
| Hero 背景图 | 图片 | 无 | 建议 2000 × 1000；选择后自动叠加暗色蒙层 |
| Hero 背景色 | 颜色选择器 | 深色 | 仅在未选背景图时生效 |
| 首页头部透明叠加 | 复选框 | 不勾选 | 让头部浮在 Hero 之上，移动端自动改实底 |
| 显示向下滚动提示 | 复选框 | 勾选 | |

### 首页区块

| 配置项 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| 显示精选作品区块 | 复选框 | 勾选 | 无标记「精选」的作品时自动退回最新作品 |
| 精选区块标题 | 文本 | `精选作品` | |
| 精选作品数量 | 数字 | `6` | 3 - 12 |
| 显示「查看全部作品」按钮 | 复选框 | 勾选 | |
| 显示服务介绍区块 | 复选框 | 勾选 | |
| 服务区块标题 | 文本 | `我能提供什么` | |
| 显示客户区块 | 复选框 | 勾选 | |
| 客户区块标题 | 文本 | `合作过的客户` | |
| 展示客户数量 | 数字 | `8` | 2 - 24 |
| 显示约稿 CTA 区块 | 复选框 | 勾选 | |
| CTA 标题 | 文本 | `有项目想聊？` | |
| CTA 说明文字 | 多行文本 | 内置默认 | |
| CTA 按钮文案 | 文本 | `发起联系` | |
| CTA 按钮链接 | URL | 无 | 不填则不输出按钮 |

### 作品网格与筛选

| 配置项 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| 桌面端列数 | 下拉 | `3 列` | 可选 2 / 3 / 4 |
| 启用分类筛选条 | 复选框 | 勾选 | |
| 筛选每页作品数 | 数字 | `12` | 4 - 48，服务端分页粒度 |
| 摘要长度（词） | 数字 | `28` | 8 - 100 |

### 作品详情页

| 配置项 | 类型 | 默认值 | 说明 |
| --- | --- | --- | --- |
| 显示作品信息卡片 | 复选框 | 勾选 | |
| 显示相关作品 | 复选框 | 勾选 | 同分类，最多 3 个 |
| 作品归档页标题 | 文本 | `作品集` | |
| 客户归档页标题 | 文本 | `合作客户` | |

### 菜单与 Widget

- **菜单位置**：主导航、页脚导航。
- **Widget 区域**：侧栏 1（主侧栏）、侧栏 2（次要）、页脚 1 / 2 / 3。

### 图片尺寸

| 名称 | 尺寸 | 用途 |
| --- | --- | --- |
| `gridcraft-tile` | 640 × 480 | 客户 Logo |
| `gridcraft-card` | 900 × 600 | 作品卡片、作品详情页头图 |
| `gridcraft-hero` | 2000 × 1000 | 首页 Hero 背景 |

## 模板层级

```
gridcraft-portfolio/
├── style.css                    # 主题头注释 + 全部样式
├── functions.php                # 装配入口
├── theme.json                   # 块编辑器配置
├── index.php                    # 通用回退模板
├── front-page.php               # 首页：Hero + 精选 + 统计 + 服务 + 客户 + CTA
├── archive-portfolio.php        # 作品归档：瀑布流 + 筛选条
├── single-portfolio.php         # 作品详情
├── archive-client.php           # 客户归档：客户 + 名下作品
├── single-client.php            # 客户详情：档案 + 作品网格
├── taxonomy-portfolio_category.php # 作品分类归档（层级，含父级面包屑）
├── taxonomy-portfolio_tag.php   # 作品标签归档（含标签云）
├── archive.php                  # 其他归档（文章分类 / 标签 / 日期）
├── single.php                   # 单篇文章（创作笔记）
├── page.php                     # 单页面
├── search.php                   # 搜索结果
├── 404.php                      # 未找到
├── comments.php                 # 评论
├── sidebar.php                  # 侧栏
├── searchform.php               # 搜索表单
├── header.php                   # <head> + 站点头部（支持透明叠加）
├── footer.php                   # 页脚 + Lightbox 容器
├── inc/
│   ├── post-types.php           # CPT 与分类法注册
│   ├── meta-boxes.php           # Meta 框注册 / 渲染 / 保存 / REST 注册
│   ├── template-tags.php        # 模板标签、SVG 图标表、Lightbox 属性注入
│   ├── ajax.php                 # admin-ajax.php 筛选处理器
│   └── customizer.php           # Customizer 面板与设置项
└── assets/
    ├── css/
    │   ├── editor-style.css     # 区块编辑器内样式
    │   └── admin.css            # 后台 Meta 框样式
    └── js/
        ├── navigation.js        # 移动端导航、锚点偏移
        ├── filter.js            # 分类筛选（原生 fetch）
        ├── lightbox.js          # 图片查看器（零依赖）
        └── customizer-preview.js# Customizer 实时预览
```

> 翻译文件（`.pot` / `.mo` / `.l10n`）放在 `languages/` 目录，该目录在首次翻译时创建。主题已调用 `load_theme_textdomain()`，放入语言包后即可生效。

### 可覆盖的模板

在子主题中创建同名文件即可覆盖，例如 `front-page.php`、`archive-portfolio.php`、`single-portfolio.php`、`taxonomy-portfolio_category.php`。

## 子主题制作

1. 在 `wp-content/themes/` 下创建目录，例如 `my-gridcraft-child`。
2. 目录内只需两个文件：

```php
<?php
/**
 * My Gridcraft 子主题样式。
 */

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'my-gridcraft-child',
		get_stylesheet_uri(),
		array( 'gridcraft-style' ),
		'1.0.0'
	);
} );
```

```css
/*
Theme Name: My Gridcraft
Description: Gridcraft Portfolio 的子主题。
Template: gridcraft-portfolio
Version: 1.0.0
*/
```

3. 启用子主题。

### 扩展点

| 名称 | 类型 | 说明 |
| --- | --- | --- |
| `gridcraft_get_option( $key, $default )` | 函数 | 读取主题设置 |
| `gridcraft_content_width` | filter | 覆盖正文宽度 |
| `gridcraft_work_facts` | filter | 增减作品信息卡片的条目 |
| `gridcraft_services` | filter | 替换首页服务列表 |
| `gridcraft_filter_query_args` | filter | 调整筛选查询的 `WP_Query` 参数 |
| `gridcraft_work_card( $args )` | 函数 | 自定义作品卡片渲染 |
| `gridcraft_work_grid( $query, $classes )` | 函数 | 整体替换网格渲染 |
| `gridcraft_inline_icon( $name )` | 函数 | 内联 SVG 图标 |
| `gridcraft_get_work_facts( $post_id )` | 函数 | 汇总作品的年份 / 客户 / 角色 / 技术栈 / 分类 |

### 替换服务列表

```php
add_filter( 'gridcraft_services', function () {
	return array(
		array(
			'icon'  => 'code',
			'title' => '前端开发',
			'text'  => '按项目报价，交付含源码与文档。',
			'price' => '面议',
		),
	);
} );
```

### 增加筛选维度

```php
add_filter( 'gridcraft_filter_query_args', function ( $args ) {
	if ( ! empty( $_GET['year'] ) ) {
		$args['meta_query'] = array(
			array(
				'key'   => '_gridcraft_year',
				'value' => sanitize_text_field( wp_unslash( $_GET['year'] ) ),
			),
		);
	}
	return $args;
} );
```

## FAQ

**Q：作品的固定链接是 `/work/作品名/`，能改吗？**
可以。进入 **设置 → 固定链接**，或用过滤器：

```php
add_filter( 'post_type_link', function ( $url, $post ) {
	if ( 'portfolio' === $post->post_type ) {
		return str_replace( '/work/', '/projects/', $url );
	}
	return $url;
}, 10, 2 );
```

改完后到 **设置 → 固定链接** 点一次「保存更改」刷新规则。

**Q：作品分类和文章分类能共用吗？**
默认不共用。`portfolio_category` 是独立的层级分类法。如果确实想共用，可以移除注册：

```php
add_action( 'init', function () {
	unregister_taxonomy( 'portfolio_category', 'portfolio' );
}, 20 );
```

移除后作品的分类筛选条会失效（`gridcraft_filter_bar()` 检测到分类数不足 2 个就不输出）。

**Q：首页没有内容区块。**
首页的每个区块都有前置条件：精选区块需要至少 1 篇已发布作品（没有标记「精选」时自动退回最新作品），服务区块和 CTA 不依赖数据，客户区块需要至少 1 个 `client` 条目。先发布几篇作品和一个客户，再来看首页。

**Q：筛选条点了没反应？**
打开浏览器控制台看 Network 面板。正常情况会看到一条 `admin-ajax.php` 请求。如果返回 403，是 Nonce 过期（页面缓存了太久），刷新页面即可。如果返回「加载失败」，检查是否有安全插件拦截了 `admin-ajax.php`。

**Q：瀑布流是 CSS 还是 JS？**
CSS `column-count`，没有 JS。好处是不会因为图片加载完成触发重排（`grid-js` 类方案常见的跳动问题）。代价是同一列内的项目从上到下填充，与 Grid 布局的按行填充顺序不同 — 对作品集来说通常更好，因为视觉上是「顺着往下看」。

**Q：Lightbox 的图片顺序怎么定的？**
按文档顺序收集所有带 `data-gc-lightbox-item` 的图片。主题会给作品详情页正文里的图片自动加这个属性。如果某张图想排除，把它包在 `<a>` 里就行 — 脚本会跳过已是链接的图片。

**Q：支持 WooCommerce 吗？**
声明了 `woocommerce` 支持，启用不会报错，但**没有**做模板适配。

**Q：screenshot.png 必须吗？**
上架 WordPress.org 目录必须提供，尺寸 1200 × 900。本地自用可以不放。

## License

MIT License

Copyright (c) 2026 MornRain

详细条款见 [LICENSE](LICENSE) 文件。

本主题为 MornRain 独立开发，不捆绑任何第三方库。所有图标为内联 SVG 字面量，字体使用系统字体栈，无任何外部资源请求。
