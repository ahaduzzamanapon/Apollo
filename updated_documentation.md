# Laravel Admin Panel Documentation

**Tech Stack**

* Laravel (Backend & Blade templating)
* Bootstrap 5 (via CDN, no NPM)
* Blade Components & Layouts
* MySQL / PostgreSQL

This document explains how to build a **responsive Admin Panel** with:

* Frontend & Backend separation
* Authentication (Admin Login & Registration)
* Left Sidebar with Multi-level Tree Menu
* Role & Permission Management
* CRUD system
* Color theming and responsive design

---

## 1. Project Structure

```
app/
 ├── Http/
 │   ├── Controllers/
 │   │   ├── Admin/
 │   │   │   ├── AuthController.php
 │   │   │   ├── DashboardController.php
 │   │   │   ├── RoleController.php
 │   │   │   ├── PermissionController.php
 │   │   │   └── UserController.php
 │   └── Middleware/
 │       └── AdminMiddleware.php
resources/
 ├── views/
 │   ├── admin/
 │   │   ├── auth/
 │   │   ├── layouts/
 │   │   ├── dashboard.blade.php
 │   │   ├── roles/
 │   │   └── users/
 │   └── frontend/
 │       └── home.blade.php
routes/
 ├── web.php
 └── admin.php
```

---

## 2. Frontend vs Backend

### Frontend

* Public pages (Home, About, Contact)
* No authentication required

### Backend (Admin Panel)

* Protected routes
* Admin authentication
* Sidebar layout
* Role-based access

---

## 3. Authentication (Admin)

### Migration

```bash
php artisan make:migration create_admins_table
```

```php
Schema::create('admins', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamps();
});
```

### Admin Model

```php
class Admin extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password'];
}
```

### Auth Controller

```php
class AuthController extends Controller
{
    public function login() {
        return view('admin.auth.login');
    }

    public function loginPost(Request $request) {
        if (Auth::guard('admin')->attempt($request->only('email','password'))) {
            return redirect()->route('admin.dashboard');
        }
        return back();
    }
}
```

---

## 4. Routes

### admin.php

```php
Route::prefix('admin')->group(function () {
    Route::get('login', [AuthController::class, 'login']);
    Route::post('login', [AuthController::class, 'loginPost']);

    Route::middleware('admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');
    });
});
```

---

## 5. Admin Layout (Blade + Bootstrap CDN)

### Bootstrap CDN

```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
```

### Layout Structure

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    @include('admin.layouts.styles')
</head>
<body class="bg-light">
<div class="d-flex">
    @include('admin.layouts.sidebar')
    <main class="flex-fill p-4">
        @yield('content')
    </main>
</div>
</body>
</html>
```

---

## 6. Sidebar (Left Menu)

### Design Goals

* Fixed left sidebar
* Responsive collapse
* Root color theme

### Sidebar Example

```blade
<div class="sidebar bg-dark text-white">
    <ul class="nav flex-column">
        <li class="nav-item"><a href="#" class="nav-link">Dashboard</a></li>
        <li class="nav-item">
            <a data-bs-toggle="collapse" href="#userMenu" class="nav-link">Users</a>
            <ul class="collapse" id="userMenu">
                <li><a href="#">All Users</a></li>
                <li><a href="#">Add User</a></li>
            </ul>
        </li>
    </ul>
</div>
```

---

## 7. Multi-Level Tree Menu

### Database Table

```php
Schema::create('menus', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('route')->nullable();
    $table->foreignId('parent_id')->nullable();
    $table->integer('order')->default(0);
    $table->timestamps();
});
```

### Recursive Blade

```blade
@foreach($menus as $menu)
    <li>
        <a href="{{ $menu->route ?? '#' }}">{{ $menu->title }}</a>
        @if($menu->children)
            <ul>
                @include('admin.layouts.menu', ['menus' => $menu->children])
            </ul>
        @endif
    </li>
@endforeach
```

---

## 8. Role & Permission System

### Tables

* roles
* permissions
* role_permission
* admin_role

```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name');
});
```

### Middleware

```php
if (!auth()->user()->hasPermission('edit-user')) {
    abort(403);
}
```

---

## 9. CRUD Example (Users)

### Controller

```php
class UserController extends Controller
{
    public function index() {
        return view('admin.users.index', ['users' => User::all()]);
    }

    public function store(Request $request) {
        User::create($request->all());
        return back();
    }
}
```

### Blade View

```blade
<form method="POST">
    @csrf
    <input name="name" class="form-control">
    <button class="btn btn-primary">Save</button>
</form>
```

---

## 10. Responsive Design

* Use Bootstrap grid
* Sidebar collapses on mobile
* Use `offcanvas` for mobile sidebar

---

## 11. Color Theme (Root Variables)

```css
:root {
    --primary-color: #4f46e5;
    --sidebar-bg: #111827;
}
```

---

## 12. Security Best Practices

* Use middleware for admin routes
* Hash passwords
* Validate requests
* CSRF protection

---

## 13. CRUD Builder (Code Generator)

The Admin Panel includes a **CRUD Builder** that automatically generates:

* Model
* Migration
* Controller
* Views (index, create, edit)
* Routes
* Menu entry

This builder uses **stub files** and a dynamic UI to define fields, relations, and UI types.

---

## 13.1 CRUD Builder Routes

```php
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('crud-builder', [CrudBuilderController::class, 'index'])->name('crud-builder.index');
    Route::post('crud-builder/generate', [CrudBuilderController::class, 'generate'])->name('crud-builder.generate');
    Route::get('crud-builder/models', [CrudBuilderController::class, 'getModels'])->name('crud-builder.get-models');
    Route::get('crud-builder/model-columns', [CrudBuilderController::class, 'getModelColumns'])->name('crud-builder.get-model-columns');
});
```

---

## 13.2 CRUD Builder Controller

The `CrudBuilderController`:

* Reads available models
* Detects database columns
* Generates files using stub templates
* Registers routes automatically
* Adds menu entries dynamically

Key features:

* Supports relationships (`foreignId`)
* Image upload handling
* Validation rules generation
* Soft deletes
* Pagination

---

## 13.3 Stub Files Structure

Create a `resources/stubs` directory:

```
resources/stubs/
 ├── model.stub
 ├── migration.stub
 ├── controller.stub
 ├── index.stub
 ├── create.stub
 └── edit.stub
```

---

## 13.4 Model Stub Example

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
{{ softDeleteImport }}

class {{ modelName }} extends Model
{
    {{ softDeleteTrait }}

    protected $table = '{{ tableName }}';

    protected $fillable = [{{ fillable }}];

{{ relationships }}
}
```

---

## 13.5 Migration Stub Example

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('{{ tableName }}', function (Blueprint $table) {
            $table->id();
{{ fields }}
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{{ tableName }}');
    }
};
```

---

## 13.6 Controller Stub Example

```php
<?php

namespace {{ namespace }};

use {{ modelNamespace }}\{{ modelName }};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class {{ className }} extends Controller
{
    public function index()
    {
        $items = {{ modelName }}::paginate(10);
        return view('admin.{{ modelVariablePlural }}.index', compact('items'));
    }

    public function create()
    {
{{ relations }}
        return view('admin.{{ modelVariablePlural }}.create'{{ compact }});
    }

    public function store(Request $request)
    {
        $data = $request->validate([
{{ rules }}
        ]);

        $data = $request->except({{ except }});
{{ storeImage }}
        {{ modelName }}::create($data);
        return redirect()->route('{{ modelVariablePlural }}.index');
    }
}
```

---

## 13.7 View Stubs

### Index Stub

* Table generation
* Action buttons (edit/delete)
* Image preview
* Relationship display

### Create & Edit Stub

* Dynamic form fields
* CKEditor (via CDN)
* Image upload support
* Relation dropdowns

---

## 13.8 CRUD Builder UI

The CRUD Builder UI provides:

* Field builder table
* DB type & HTML type mapping
* Relation selector (Model → Column)
* Icon picker for menu
* Pagination, soft delete & migration toggles

The UI is fully responsive and uses **Bootstrap + jQuery CDN** only.

---

## 13.9 Menu Auto Registration

After CRUD generation:

* A new menu entry is inserted
* Order is auto-incremented
* Icon is applied
* Route is linked automatically

---

## 13.10 Result

With one form submission, the system generates:

* Production-ready CRUD
* Clean Laravel architecture
* Consistent admin UI
* Fully wired routes, menus, and permissions

---

## 14. Final Notes

This admin panel:

* Uses no NPM
* Fully Blade + Bootstrap CDN
* Supports roles, permissions, tree menus, and CRUD
* Responsive and scalable

---

You can extend this by adding:**

Settings module
Theme switcher (Dark/Light)
