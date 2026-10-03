# Changelog

本项目遵循 [Semantic Versioning](https://semver.org/lang/zh-CN/) 规范。

## [Unreleased]

### 修复

- 补齐模板层级：新增 `archive-client.php`、`single-client.php`、`taxonomy-portfolio_category.php`、`taxonomy-portfolio_tag.php`，`client` 类型与两个作品分类法不再回落到通用归档模板。
- `gridcraft_work_grid()`消费完查询后补上 `wp_reset_postdata()`，传入主查询时不再污染全局 `$post`。
- 分类筛选按钮补上 `aria-pressed`，`setActiveButton()` 同步该状态；Lightbox 触发图片补上 `tabindex="0"` `role="button"` 并支持 Enter / 空格打开。
- Hero 内联样式的背景图改用 `esc_url_raw()`、背景色改用 `sanitize_hex_color()` 后再进 `esc_attr()`。
- `single-portfolio.php` / `taxonomy-*.php` 中属性片段拼接补 `phpcs:ignore` 说明，避免误改转义。
- `--gc-muted` 由 `#8a8c93` 调整为 `#73747a`，正文对比度从 3.36:1 提升到 4.66:1（WCAG AA）。
- `get_previous_post( true, ... )` 改为 `get_previous_post()`：原写法会把第一个参数当作 `$deprecated`，触发 `_deprecated_argument()`。
- 资源版本号改用 `filemtime()`，改CSS / JS 后不再依赖手动改版本号刷新缓存。

## [1.0.0] - 2026-10-02

### 新增

- 完整模板层级：`index.php` / `front-page.php` / `archive-portfolio.php` / `single-portfolio.php` / `archive.php` / `single.php` / `page.php` / `search.php` / `404.php` / `comments.php` / `sidebar.php` / `searchform.php` / `header.php` / `footer.php`。
- `theme.json`：2 个字体栈、4 档字号、8 组配色、布局宽度配置。
- 注册两个自定义文章类型：
  - `portfolio`（作品）：支持缩略图、摘要、评论、页面属性，`show_in_rest` 开启。
  - `client`（客户）：支持缩略图、页面属性，`show_in_rest` 开启。
- 注册两个分类法：`portfolio_category`（层级式作品分类）、`portfolio_tag`（扁平式作品标签），均支持 REST API 与后台列表列。
- 作品 Meta 框：项目链接（URL）、客户（下拉关联）、年份、承担角色、技术栈（多行）、精选标记（复选框），全部做 Nonce 校验 + `current_user_can()` 权限校验 + 分类型清洗，并通过 `register_post_meta()` 注册到 REST API。
- 客户 Meta 框：官网地址（URL）、所属行业。
- 瀑布流作品网格：CSS `column-count` 实现，桌面端 2 / 3 / 4 列可配，900px 以下 2 列，640px 以下 1 列，无 JS 高度计算。
- 分类筛选条：原生 `fetch` 请求 `admin-ajax.php`（带 Nonce 校验），结果数不超过已渲染数量时走客户端瞬时筛选，否则服务端分页渲染，失败自动降级为分类归档链接。
- 手写 Lightbox：键盘导航（`Esc` / `←` / `→`）、焦点陷阱、body 滚动锁定、点击遮罩关闭，零第三方依赖。
- 作品详情页 `single-portfolio.php`：信息卡片（年份 / 客户 / 角色 / 技术栈 / 分类）、访问项目按钮、技术栈标签、上一个 / 下一个作品、同分类相关作品。
- 首页区块：Hero（背景图 / 背景色 / 暗色蒙层 / 透明叠加头部）、精选作品、数据统计条、服务介绍、客户墙、约稿 CTA，每个区块可独立开关。
- 统计条的作品数、客户数、年份跨度从真实数据计算；服务列表与统计条均提供过滤器扩展点。
- 全部图标为内联 SVG 字面量表（9 个），无图标字体、无外部资源。
- `wp_kses_allowed_html` 补充 SVG 标签白名单。
- Widget 区域：侧栏 2 个 + 页脚 3 个。
- `after_switch_theme` 时自动刷新固定链接，作品页 URL 启用即用。
- 全站转义输出，PHP 7.4 兼容，零第三方库。
