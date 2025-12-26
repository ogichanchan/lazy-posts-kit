**1. Plugin Name:**
Lazy Posts Kit

**2. Short Description:**
A unique, PHP-only WordPress utility plugin that displays posts in a "lazy" style using a simple shortcode, focused on simplicity and efficiency.

**3. Detailed Description:**
Lazy Posts Kit is a lightweight and efficient WordPress plugin designed to display lists of posts, pages, or any custom post types using a simple shortcode. True to its "PHP-only" nature, it avoids JavaScript and complex frameworks, providing a clean and fast solution for showcasing your content.

**Key Features:**
*   **Simple Shortcode Usage:** Easily embed a customizable list of posts anywhere on your site with `[lazy_posts_kit]`.
*   **Customizable Display:** Control the number of posts, post types, order, sorting, and visibility of elements like thumbnails, excerpts, and dates.
*   **Admin Settings Page:** Set global default options for the shortcode, allowing consistent behavior across your site without repetitive attribute declarations.
*   **PHP-Only:** Built purely with PHP, ensuring minimal overhead and maximum compatibility, without relying on external libraries or complex scripts.
*   **Flexible Styling:** Includes basic inline CSS for a clean, responsive layout out-of-the-box, which can be easily overridden by your theme or custom CSS.
*   **Semantic HTML:** Outputs content using standard HTML tags, improving accessibility and SEO.

**How it Works:**
Upon activation, the plugin creates a settings page under "Settings > Lazy Posts Kit" in your WordPress admin area. Here, you can define default values for how your post lists will appear, such as posts per page, default post types, and whether to show thumbnails or excerpts.

When you use the `[lazy_posts_kit]` shortcode, it fetches and displays posts based on these default settings. You can, however, override any of these defaults directly within the shortcode attributes for specific instances.

**Shortcode Attributes (Override Global Settings):**
*   `posts_per_page="5"`: Number of posts to display.
*   `post_type="post,page,product"`: Comma-separated list of post types.
*   `order_by="date"`: How to order the posts (e.g., date, title, rand).
*   `order="DESC"`: Sorting order (ASC or DESC).
*   `show_thumbnail="true"`: Display post featured image (true/false).
*   `show_excerpt="true"`: Display post excerpt (true/false).
*   `show_date="true"`: Display post publication date (true/false).
*   `title_tag="h2"`: HTML tag for post titles (e.g., h1-h6, p, div, span).

**Example Usage:**
To display 3 pages and posts, ordered randomly, showing thumbnails and dates with H3 titles:
`[lazy_posts_kit posts_per_page="3" post_type="post,page" order_by="rand" show_thumbnail="true" show_date="true" title_tag="h3"]`

Lazy Posts Kit is ideal for users looking for a straightforward, performant, and easily customizable way to list posts on their WordPress site without bloat.

**4. GitHub URL:**
https://github.com/ogichanchan/lazy-posts-kit