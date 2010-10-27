<h1>Pages, Layouts, Modules &amp; Blocks Explained</h1>
<p>The following are definitions used to explain the more important aspects of FUEL CMS.</p>

<h2>Pages</h2>
<p>Pages combine layout and variable data to form a web page. Along with the layout variable data, pages have a location (the URI of the page), 
published (whether to display the page), and a cache setting property (whether to cache the file to speed up display). </p>

<h2>Layouts</h2>
<p>Layouts are created by modifying the <dfn>config/MY_fuel_layouts.php</dfn> file. The layout determines what variable data can be used for the page.</p>

<h2>Modules</h2>
<p>Modules can range from simple data models that can be edited inside FUEL, to more complex applications that contain their own controllers, views, libraries, etc 
(e.g. the blog).</p>

<h2>Blocks</h2>
<p>Blocks (or partials), are essentially reusable view elements of the site that get used across pages (e.g. a header and footer).</p>