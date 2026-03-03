# Laravel Migration Guide

## Migration Strategy: PHP → Laravel + Livewire

**Timeline:** 16 weeks (2 jam/hari = ~224 jam total)

---

## Phase 1: Foundation (Week 1-2)

### Week 1: Setup & Database

**Tasks:**
1. Install Laravel 10.x
2. Configure database connection
3. Create database migrations
4. Setup authentication (Laravel Breeze/Jetstream)
5. Create base models

**Deliverables:**
- [ ] Laravel project initialized
- [ ] Database migrations created
- [ ] Authentication working
- [ ] Base models created

**Time Estimate:** 14 hours

---

#### 1.1 Laravel Installation

```bash
composer create-project laravel/laravel helpdesk-laravel
cd helpdesk-laravel
```

**Configure `.env`:**
```env
APP_NAME="IT Helpdesk"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=it_helpdesk_dev
DB_USERNAME=root
DB_PASSWORD=your_password
```

---

#### 1.2 Database Migrations

**Create migrations for all tables:**

```bash
php artisan make:migration create_clients_table
php artisan make:migration create_people_table
php artisan make:migration create_assets_table
php artisan make:migration create_licenses_table
php artisan make:migration create_tickets_table
# ... etc
```

**Example Migration (clients):**

```php
// database/migrations/xxxx_create_clients_table.php
public function up()
{
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('asset_tag_prefix', 50)->nullable();
        $table->string('license_tag_prefix', 50)->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
        
        $table->index('name');
    });
}
```

**Example Migration (people):**

```php
// database/migrations/xxxx_create_people_table.php
public function up()
{
    Schema::create('people', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->enum('type', ['user', 'admin']);
        $table->foreignId('roleid')->nullable()->constrained('roles');
        $table->foreignId('clientid')->nullable()->constrained('clients');
        $table->string('email')->unique();
        $table->string('ldap_user')->nullable();
        $table->string('title')->nullable();
        $table->string('mobile', 50)->nullable();
        $table->string('password');
        $table->string('theme', 50)->default('skin-blue');
        $table->string('sidebar', 50)->default('opened');
        $table->string('layout', 50)->nullable();
        $table->text('notes')->nullable();
        $table->text('signature')->nullable();
        $table->string('sessionid')->nullable();
        $table->string('resetkey')->nullable();
        $table->binary('avatar')->nullable();
        $table->integer('autorefresh')->default(0);
        $table->string('lang', 10)->default('en');
        $table->boolean('ticketsnotification')->default(0);
        $table->string('fcmtoken')->nullable();
        $table->rememberToken();
        $table->timestamps();
        
        $table->index('email');
        $table->index('type');
        $table->index('clientid');
    });
}
```

**Run migrations:**
```bash
php artisan migrate
```

---

#### 1.3 Create Base Models

```bash
php artisan make:model Client
php artisan make:model Person
php artisan make:model Asset
php artisan make:model License
php artisan make:model Ticket
# ... etc
```

**Example Model (Client):**

```php
// app/Models/Client.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'asset_tag_prefix',
        'license_tag_prefix',
        'notes',
    ];
    
    // Relationships
    public function people()
    {
        return $this->hasMany(Person::class, 'clientid');
    }
    
    public function assets()
    {
        return $this->hasMany(Asset::class, 'clientid');
    }
    
    public function licenses()
    {
        return $this->hasMany(License::class, 'clientid');
    }
    
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'clientid');
    }
    
    public function projects()
    {
        return $this->hasMany(Project::class, 'clientid');
    }
    
    public function admins()
    {
        return $this->belongsToMany(Person::class, 'clients_admins', 'clientid', 'adminid');
    }
}
```

---

### Week 2: Authentication & First Module

**Tasks:**
1. Setup Laravel Livewire
2. Create authentication system
3. Implement role-based permissions
4. Create first CRUD module (Locations)
5. Create base layout/template

**Deliverables:**
- [ ] Livewire installed
- [ ] Login/logout working
- [ ] Permission system working
- [ ] Locations CRUD complete
- [ ] Base template created

**Time Estimate:** 14 hours

---

#### 2.1 Install Livewire

```bash
composer require livewire/livewire
```

**Publish config:**
```bash
php artisan livewire:publish --config
```

---

#### 2.2 Authentication

**Option A: Laravel Breeze (Recommended)**
```bash
composer require laravel/breeze --dev
php artisan breeze:install livewire
npm install && npm run dev
php artisan migrate
```

**Option B: Manual Setup**

Create authentication controllers and views manually.

---

#### 2.3 Permission System

**Create Permission Middleware:**

```php
// app/Http/Middleware/CheckPermission.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $role = $user->role;
        $permissions = json_decode($role->perms, true) ?? [];
        
        if (!in_array($permission, $permissions)) {
            abort(403, 'Unauthorized action.');
        }
        
        return $next($request);
    }
}
```

**Register middleware:**

```php
// app/Http/Kernel.php
protected $middlewareAliases = [
    // ...
    'permission' => \App\Http\Middleware\CheckPermission::class,
];
```

**Usage in routes:**

```php
Route::middleware(['auth', 'permission:addLocation'])->group(function () {
    Route::get('/locations/create', [LocationController::class, 'create']);
});
```

---

#### 2.4 First Module: Locations

**Create Livewire Component:**

```bash
php artisan make:livewire LocationManager
```

**Component Class:**

```php
// app/Http/Livewire/LocationManager.php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Location;
use App\Models\Client;

class LocationManager extends Component
{
    public $locations;
    public $clients;
    public $name = '';
    public $clientid = '';
    public $editingId = null;
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'clientid' => 'required|exists:clients,id',
    ];
    
    public function mount()
    {
        $this->loadLocations();
        $this->clients = Client::all();
    }
    
    public function loadLocations()
    {
        $user = auth()->user();
        
        if ($user->type === 'admin' && $user->isAdmin) {
            $this->locations = Location::with('client')->get();
        } else {
            $this->locations = Location::where('clientid', $user->clientid)->get();
        }
    }
    
    public function save()
    {
        $this->validate();
        
        if ($this->editingId) {
            $location = Location::find($this->editingId);
            $location->update([
                'name' => $this->name,
                'clientid' => $this->clientid,
            ]);
            session()->flash('message', 'Location updated successfully.');
        } else {
            Location::create([
                'name' => $this->name,
                'clientid' => $this->clientid,
            ]);
            session()->flash('message', 'Location created successfully.');
        }
        
        $this->reset(['name', 'clientid', 'editingId']);
        $this->loadLocations();
    }
    
    public function edit($id)
    {
        $location = Location::find($id);
        $this->editingId = $id;
        $this->name = $location->name;
        $this->clientid = $location->clientid;
    }
    
    public function delete($id)
    {
        Location::destroy($id);
        session()->flash('message', 'Location deleted successfully.');
        $this->loadLocations();
    }
    
    public function render()
    {
        return view('livewire.location-manager');
    }
}
```

**Component View:**

```blade
<!-- resources/views/livewire/location-manager.blade.php -->
<div>
    <div class="card">
        <div class="card-header">
            <h3>Locations</h3>
        </div>
        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            
            <form wire:submit.prevent="save">
                <div class="mb-3">
                    <label>Client</label>
                    <select wire:model="clientid" class="form-control">
                        <option value="">Select Client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                    @error('clientid') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" wire:model="name" class="form-control">
                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">
                    {{ $editingId ? 'Update' : 'Create' }}
                </button>
                @if($editingId)
                    <button type="button" wire:click="$set('editingId', null)" class="btn btn-secondary">
                        Cancel
                    </button>
                @endif
            </form>
            
            <hr>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Client</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($locations as $location)
                        <tr>
                            <td>{{ $location->id }}</td>
                            <td>{{ $location->name }}</td>
                            <td>{{ $location->client->name }}</td>
                            <td>
                                <button wire:click="edit({{ $location->id }})" class="btn btn-sm btn-warning">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $location->id }})" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
```

**Route:**

```php
// routes/web.php
Route::middleware(['auth', 'permission:manageData'])->group(function () {
    Route::get('/locations', LocationManager::class)->name('locations');
});
```

---

## Phase 2: Core Modules (Week 3-8)

### Week 3-4: Clients & Users

**Tasks:**
1. Create Client CRUD (Livewire)
2. Create User CRUD (Livewire)
3. Create Staff CRUD (Livewire)
4. Implement client-staff assignment
5. Add notes functionality

**Time Estimate:** 28 hours

---

### Week 5-6: Assets & Licenses

**Tasks:**
1. Create Asset CRUD (Livewire)
2. Create License CRUD (Livewire)
3. Implement asset-license assignment
4. Add custom fields support
5. Add QR code functionality
6. Implement file uploads

**Time Estimate:** 28 hours

---

### Week 7-8: Roles & Permissions

**Tasks:**
1. Create Role CRUD
2. Implement permission management UI
3. Add permission checks throughout
4. Test multi-tenancy
5. Add attribute management (categories, manufacturers, etc.)

**Time Estimate:** 28 hours

---

## Phase 3: Complex Features (Week 9-12)

### Week 9-10: Ticketing System

**Tasks:**
1. Create Ticket CRUD (Livewire)
2. Implement ticket replies
3. Add file attachments
4. Implement status workflow
5. Add escalation rules
6. Implement auto-close
7. Add email-to-ticket (cron job)

**Time Estimate:** 28 hours

---

### Week 11: Projects & Issues

**Tasks:**
1. Create Project CRUD (Livewire)
2. Create Issue CRUD (Livewire)
3. Implement milestones
4. Add comments
5. Implement progress calculation
6. Add time logging

**Time Estimate:** 14 hours

---

### Week 12: Monitoring & Knowledge Base

**Tasks:**
1. Create Monitoring Host CRUD
2. Implement checks
3. Add alert system
4. Create Knowledge Base CRUD
5. Implement search

**Time Estimate:** 14 hours

---

## Phase 4: Integration (Week 13-14)

### Week 13: API & Notifications

**Tasks:**
1. Create API routes (backward compatible)
2. Implement API authentication
3. Setup email notifications (Laravel Mail)
4. Setup SMS notifications
5. Setup FCM push notifications
6. Migrate notification templates

**Time Estimate:** 14 hours

---

### Week 14: LDAP & File Management

**Tasks:**
1. Implement LDAP authentication
2. Setup file upload system
3. Implement file download/preview
4. Add file deletion
5. Test all integrations

**Time Estimate:** 14 hours

---

## Phase 5: Polish (Week 15-16)

### Week 15: Frontend & Testing

**Tasks:**
1. Polish UI/UX
2. Add loading states
3. Add error handling
4. Write tests (Feature tests)
5. Performance optimization

**Time Estimate:** 14 hours

---

### Week 16: Data Migration & Deployment

**Tasks:**
1. Create data migration scripts
2. Test migration on copy of production data
3. Create deployment checklist
4. Create rollback plan
5. User documentation
6. Training materials

**Time Estimate:** 14 hours

---

## Data Migration Scripts

### Migration Script Template

```php
// database/seeders/MigrateFromOldSystemSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MigrateFromOldSystemSeeder extends Seeder
{
    public function run()
    {
        // Connect to old database
        $oldDb = DB::connection('old_mysql');
        
        // Migrate clients
        $this->migrateClients($oldDb);
        
        // Migrate people (users & staff)
        $this->migratePeople($oldDb);
        
        // Migrate assets
        $this->migrateAssets($oldDb);
        
        // ... etc
    }
    
    private function migrateClients($oldDb)
    {
        $clients = $oldDb->table('clients')->get();
        
        foreach ($clients as $client) {
            DB::table('clients')->insert([
                'id' => $client->id,
                'name' => $client->name,
                'asset_tag_prefix' => $client->asset_tag_prefix,
                'license_tag_prefix' => $client->license_tag_prefix,
                'notes' => $client->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    private function migratePeople($oldDb)
    {
        $people = $oldDb->table('people')->get();
        
        foreach ($people as $person) {
            // Convert SHA1 to bcrypt (users must reset password)
            $password = Hash::make('ChangeMe123!');
            
            DB::table('people')->insert([
                'id' => $person->id,
                'name' => $person->name,
                'type' => $person->type,
                'roleid' => $person->roleid,
                'clientid' => $person->clientid,
                'email' => $person->email,
                'ldap_user' => $person->ldap_user,
                'title' => $person->title,
                'mobile' => $person->mobile,
                'password' => $password,
                'theme' => $person->theme,
                'sidebar' => $person->sidebar,
                'layout' => $person->layout,
                'notes' => $person->notes,
                'signature' => $person->signature,
                'avatar' => $person->avatar,
                'autorefresh' => $person->autorefresh,
                'lang' => $person->lang,
                'ticketsnotification' => $person->ticketsnotification,
                'fcmtoken' => $person->fcmtoken,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Send password reset emails to all users
        $this->sendPasswordResetEmails();
    }
    
    private function migrateAssets($oldDb)
    {
        $assets = $oldDb->table('assets')->get();
        
        foreach ($assets as $asset) {
            // Convert serialized customfields to JSON
            $customfields = @unserialize($asset->customfields);
            $customfieldsJson = $customfields ? json_encode($customfields) : null;
            
            DB::table('assets')->insert([
                'id' => $asset->id,
                'categoryid' => $asset->categoryid,
                'adminid' => $asset->adminid,
                'clientid' => $asset->clientid,
                'userid' => $asset->userid,
                'manufacturerid' => $asset->manufacturerid,
                'modelid' => $asset->modelid,
                'supplierid' => $asset->supplierid,
                'statusid' => $asset->statusid,
                'locationid' => $asset->locationid,
                'purchase_date' => $asset->purchase_date,
                'warranty_months' => $asset->warranty_months,
                'tag' => $asset->tag,
                'name' => $asset->name,
                'serial' => $asset->serial,
                'notes' => $asset->notes,
                'customfields' => $customfieldsJson,
                'qrvalue' => $asset->qrvalue,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
```

**Configure old database connection:**

```php
// config/database.php
'connections' => [
    // ... existing connections
    
    'old_mysql' => [
        'driver' => 'mysql',
        'host' => env('OLD_DB_HOST', '127.0.0.1'),
        'port' => env('OLD_DB_PORT', '3306'),
        'database' => env('OLD_DB_DATABASE', 'it_helpdesk'),
        'username' => env('OLD_DB_USERNAME', 'root'),
        'password' => env('OLD_DB_PASSWORD', ''),
    ],
],
```

**Run migration:**
```bash
php artisan db:seed --class=MigrateFromOldSystemSeeder
```

---

## Deployment Checklist

### Pre-Deployment

- [ ] All features tested
- [ ] Data migration tested on copy
- [ ] Backup production database
- [ ] Backup production files
- [ ] Document rollback procedure
- [ ] Notify users of maintenance window

### Deployment

- [ ] Put old system in maintenance mode
- [ ] Final database backup
- [ ] Run data migration
- [ ] Copy uploaded files
- [ ] Deploy Laravel application
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Test critical features
- [ ] Send password reset emails

### Post-Deployment

- [ ] Monitor error logs
- [ ] Monitor performance
- [ ] User support ready
- [ ] Document known issues
- [ ] Plan for fixes

---

## Rollback Plan

If migration fails:

1. **Stop Laravel application**
2. **Restore old database from backup**
3. **Restore old files from backup**
4. **Point domain back to old system**
5. **Notify users**
6. **Analyze what went wrong**
7. **Fix issues**
8. **Schedule new migration date**

---

## Key Laravel Packages to Use

```json
{
    "require": {
        "laravel/framework": "^10.0",
        "livewire/livewire": "^3.0",
        "spatie/laravel-permission": "^5.0",
        "intervention/image": "^2.7",
        "maatwebsite/excel": "^3.1",
        "barryvdh/laravel-dompdf": "^2.0",
        "laravel/sanctum": "^3.0"
    }
}
```

---

**Document Version:** 1.0  
**Last Updated:** 2025-11-22
