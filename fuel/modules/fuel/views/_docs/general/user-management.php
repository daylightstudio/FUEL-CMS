<h1>User Management</h1>
<p>FUEL CMS initially comes with a single "super admin" user. This user is not assigned permissions because they automatically have permissions for everything.
However, in most cases you will need to create additional users and assign them appropriate permissions. Users are assigned permissions directly as opposed to being assigned
a specific role that already has permissions assigned to it (e.g. "editor"). 
If you are looking for group access permissions, you may be interested in 
<a href="https://github.com/imknight/FUEL-CMS-Modules" target="_blank">imknight's module</a>.</p>

<h2 id="creating_users">Creating Users</h2>
<p>To create a user, click on the <dfn>Users</dfn> module in the left menu of the CMS and then click the <dfn>Create</dfn> button. Enter in the users required 
information including the password and assign the appropriate permissions. After properly entering in the password fields, a checkbox will appear that if checked,
will send an email to the user with their password.</p>

<p class="important">One must be careful in assigning permissions. A user with the permissions to both manage users and their permissions will give that user a "super admin" type role.
A user with the permission to manage users, will be restricted to assigning the same permissions that they have access to.
</p>

<h2>Creating Permissions</h2>
<p>When creating a user in the CMS, sometimes a permission does not appear in the list you are needing and needs to be created. To create a permission, click on the 
<dfn>Permissions</dfn> module in the left menu of the CMS and click on the <dfn>Create</dfn> button. Then, in the name field enter in the key name of the module (e.g. a news module would have the value "news").
The description field is used to better explain the permission but if left blank, will use the name value. After creating the permission, go back to the user in the <dfn>Users</dfn> module and assign the permission.
</p>

<h2 id="assigning_permissions">Assigning Permissions to Simple Modules</h2>
<p>Each simple module in the CMS should have a permission created for it. When configuring your module in the <span class="file">fuel/application/config/MY_fuel_modules.php</span>,
you have the option of assigning the name of that permission. By default, it is the same name as the module's name (e.g "news"). In some cases you may want to create 
multiple permissions for a module that pertains to editing, publishing and deleting. To do this, you set the edit permission to the name of the module and then add the name of the other
permissions to associate to the module. You can also map the names of a permission to a different permission by assigning the key as the name of the permission and the association as the value.
The <strong>Pages</strong> module is a good example of this and it's permission setting looks like the following:
</p>
<pre class="brush:php">
...
// short hand
permission' => array('pages', 'create', 'edit', 'pages/upload' => 'pages/create', 'publish', 'delete'),

// long hand
permission' => array('pages', 'pages/create', 'pages/edit', 'pages/upload' => 'pages/create', 'pages/publish', 'pages/delete'),
...
</pre>

<h2 id="login_as_another">Login As Another User</h2>
<p>If you are a "Super Admin" user, meaning that you have the "super_admin" field set to yes in the fuel_users table, you will see an additional action in the list view next to other user name
that says <dfn>LOGIN AS</dfn>. Clicking on this allows you to easily take on the role of another user to test out their permissions and diagnose user specific problems.
To logout and resume your role as the "Super Admin", click the <dfn>Restore original user</dfn> link at the top near your user name.
</p>