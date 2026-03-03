# Quick Reference Guide

## 🚀 One-Page Cheat Sheet

---

## Decision Matrix

| Criteria | Laravel + Livewire | NestJS | Plain Express |
|----------|-------------------|---------|---------------|
| **Solo Dev Friendly** | ✅ Excellent | ⚠️ Medium | ⚠️ Medium |
| **Learning Curve** | ✅ Low | ❌ High | ⚠️ Medium |
| **Timeline** | ✅ 4 months | ❌ 12+ months | ❌ 8+ months |
| **Risk Level** | ✅ Low | ❌ High | ❌ High |
| **PHP Knowledge Reuse** | ✅ Yes | ❌ No | ❌ No |
| **Modern Stack** | ✅ Yes | ✅ Yes | ✅ Yes |
| **Performance** | ✅ Good | ✅ Good | ✅ Good |
| **Ecosystem** | ✅ Mature | ✅ Growing | ⚠️ Fragmented |
| **Incremental Migration** | ✅ Yes | ❌ No | ❌ No |

**Winner:** Laravel + Livewire ✅

---

## System Stats

```
Modules:        25+
Tables:         40+
API Endpoints:  36
Classes:        25+
Timeline:       16 weeks
Effort:         224 hours
Daily Time:     2 hours
Risk:           Medium
```

---

## Core Modules Priority

### Phase 1 (Week 1-2) - Foundation
```
1. Setup Laravel
2. Database migrations
3. Authentication
4. Locations (first CRUD)
```

### Phase 2 (Week 3-8) - Core
```
1. Clients
2. Users & Staff
3. Assets
4. Licenses
5. Roles & Permissions
6. Attributes (categories, manufacturers, etc.)
```

### Phase 3 (Week 9-12) - Complex
```
1. Tickets (most complex)
2. Projects & Issues
3. Monitoring
4. Knowledge Base
```

### Phase 4 (Week 13-14) - Integration
```
1. API (backward compatible)
2. Notifications (Email, SMS, FCM)
3. LDAP
4. File Management
```

### Phase 5 (Week 15-16) - Polish
```
1. UI/UX
2. Testing
3. Data Migration
4. Deployment
```

---

## Critical Business Logic

### Ticket Workflow
```
Create → Open → In Progress → Answered → Closed
                    ↓             ↓
                    └─────────────┴→ Reopened
```

### Email-to-Ticket
```
1. Fetch email (IMAP)
2. Parse sender, subject, body
3. Match existing ticket (by number in subject)
4. If match: Add reply
5. If no match: Create new ticket
6. Route to department (by To: email)
7. Send notifications
```

### Escalation Rules
```
Conditions: Status + Priority + Time/DateTime
Actions: Change status/priority, Assign, Notify, Add reply
Types: Global (ticketid=0) or Ticket-specific
```

### Multi-Tenancy
```
Admin (isAdmin=true) → Access ALL clients
Admin (isAdmin=false) → Access assigned clients only
User → Access own client only
```

### Asset-License Assignment
```
License (seats=5) can be assigned to max 5 assets
One asset can have multiple licenses
Track in licenses_assets table
```

---

## Database Quick Reference

### Core Tables
```
people          - Users & Staff
clients         - Organizations
assets          - IT Assets
licenses        - Software Licenses
tickets         - Support Tickets
tickets_replies - Ticket Conversation
projects        - Projects
issues          - Project Tasks
```

### Lookup Tables
```
assetcategories, licensecategories
statuslabels, manufacturers, models
suppliers, locations
tickets_departments
```

### Relationship Tables
```
licenses_assets     - License ↔ Asset
clients_admins      - Client ↔ Staff
projects_admins     - Project ↔ Staff
hosts_people        - Host ↔ People (alerts)
```

### System Tables
```
roles               - Roles & Permissions
config              - App Configuration
api_keys            - API Authentication
systemlog           - Activity Log
emaillog, smslog    - Communication Log
```

---

## API Quick Reference

### Authentication
```http
POST /api/index.php
key=YOUR_API_KEY
```

### Methods
```
get     - Retrieve resources
add     - Create resource
edit    - Update resource
delete  - Remove resource
attach  - Create relationship
detach  - Remove relationship
```

### Common Resources
```
clients, assets, licenses, tickets
users, staff, projects, issues
asset_categories, license_categories
manufacturers, models, suppliers, locations
```

### Response Codes
```
10  - Added successfully
20  - Updated successfully
30  - Deleted successfully
902 - API key missing
903 - Invalid API key
905 - Not authorized
```

---

## Laravel Commands Cheat Sheet

### Setup
```bash
composer create-project laravel/laravel helpdesk-laravel
cd helpdesk-laravel
composer require livewire/livewire
php artisan breeze:install livewire
npm install && npm run dev
```

### Migrations
```bash
php artisan make:migration create_clients_table
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh
```

### Models
```bash
php artisan make:model Client
php artisan make:model Client -m  # with migration
php artisan make:model Client -mf # with migration & factory
```

### Livewire
```bash
php artisan make:livewire LocationManager
php artisan make:livewire Clients/ClientList
```

### Controllers
```bash
php artisan make:controller ClientController
php artisan make:controller Api/ClientController --api
```

### Middleware
```bash
php artisan make:middleware CheckPermission
```

### Seeders
```bash
php artisan make:seeder ClientSeeder
php artisan db:seed
php artisan db:seed --class=ClientSeeder
```

### Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
```

### Queue
```bash
php artisan queue:work
php artisan queue:listen
php artisan queue:restart
```

---

## Livewire Component Template

```php
// app/Http/Livewire/LocationManager.php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Location;

class LocationManager extends Component
{
    public $locations;
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
    }
    
    public function loadLocations()
    {
        $this->locations = Location::all();
    }
    
    public function save()
    {
        $this->validate();
        
        if ($this->editingId) {
            Location::find($this->editingId)->update([
                'name' => $this->name,
                'clientid' => $this->clientid,
            ]);
        } else {
            Location::create([
                'name' => $this->name,
                'clientid' => $this->clientid,
            ]);
        }
        
        $this->reset(['name', 'clientid', 'editingId']);
        $this->loadLocations();
        session()->flash('message', 'Saved successfully.');
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
        $this->loadLocations();
        session()->flash('message', 'Deleted successfully.');
    }
    
    public function render()
    {
        return view('livewire.location-manager');
    }
}
```

---

## Model Relationship Template

```php
// app/Models/Client.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'asset_tag_prefix', 'license_tag_prefix', 'notes'];
    
    // One-to-Many
    public function assets()
    {
        return $this->hasMany(Asset::class, 'clientid');
    }
    
    // Many-to-Many
    public function admins()
    {
        return $this->belongsToMany(Person::class, 'clients_admins', 'clientid', 'adminid');
    }
    
    // Belongs To
    // (defined in child model)
}

// app/Models/Asset.php
class Asset extends Model
{
    // Belongs To
    public function client()
    {
        return $this->belongsTo(Client::class, 'clientid');
    }
}
```

---

## Permission Check Template

```php
// Middleware
Route::middleware(['auth', 'permission:addAsset'])->group(function () {
    Route::get('/assets/create', [AssetController::class, 'create']);
});

// In Controller
public function store(Request $request)
{
    $this->authorize('addAsset'); // Using Laravel Policy
    // or
    if (!in_array('addAsset', auth()->user()->role->permissions)) {
        abort(403);
    }
}

// In Livewire
public function mount()
{
    if (!in_array('addAsset', auth()->user()->role->permissions)) {
        abort(403);
    }
}

// In Blade
@can('addAsset')
    <button>Add Asset</button>
@endcan
```

---

## Multi-Tenancy Template

```php
// Global Scope (automatic filtering)
// app/Models/Asset.php
protected static function booted()
{
    static::addGlobalScope('client', function ($query) {
        $user = auth()->user();
        
        if ($user->type === 'user') {
            $query->where('clientid', $user->clientid);
        } elseif ($user->type === 'admin' && !$user->isAdmin) {
            $assignedClients = $user->assignedClients->pluck('id');
            $query->whereIn('clientid', $assignedClients);
        }
        // If isAdmin, no filter (access all)
    });
}

// Manual Check
public function store(Request $request)
{
    $user = auth()->user();
    
    if ($user->type === 'user' && $request->clientid != $user->clientid) {
        abort(403, 'Cannot access other client data');
    }
    
    if ($user->type === 'admin' && !$user->isAdmin) {
        $assignedClients = $user->assignedClients->pluck('id');
        if (!$assignedClients->contains($request->clientid)) {
            abort(403, 'Cannot access this client');
        }
    }
}
```

---

## Data Migration Template

```php
// database/seeders/MigrateDataSeeder.php
public function run()
{
    $oldDb = DB::connection('old_mysql');
    
    // Migrate with progress bar
    $clients = $oldDb->table('clients')->get();
    $bar = $this->command->getOutput()->createProgressBar(count($clients));
    
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
        $bar->advance();
    }
    
    $bar->finish();
    $this->command->info("\nClients migrated successfully!");
}
```

---

## Testing Template

```php
// tests/Feature/LocationTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Location;
use App\Models\User;

class LocationTest extends TestCase
{
    public function test_user_can_create_location()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post('/locations', [
            'name' => 'Test Location',
            'clientid' => 1,
        ]);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('locations', [
            'name' => 'Test Location',
        ]);
    }
    
    public function test_user_cannot_access_other_client_location()
    {
        $user = User::factory()->create(['clientid' => 1]);
        $location = Location::factory()->create(['clientid' => 2]);
        
        $response = $this->actingAs($user)->get("/locations/{$location->id}");
        
        $response->assertStatus(403);
    }
}
```

---

## Deployment Commands

```bash
# On server
cd /var/www/helpdesk-laravel

# Pull latest code
git pull origin main

# Install dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
php artisan queue:restart

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

---

## Troubleshooting

### Common Issues

**Issue:** Class not found
```bash
composer dump-autoload
```

**Issue:** Permission denied
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Issue:** Config cached
```bash
php artisan config:clear
php artisan cache:clear
```

**Issue:** Route not found
```bash
php artisan route:clear
php artisan route:cache
```

**Issue:** View not updating
```bash
php artisan view:clear
```

**Issue:** Database connection failed
```bash
# Check .env file
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## Performance Tips

### Database
```php
// Use eager loading
$assets = Asset::with('client', 'user', 'category')->get();

// Use select to limit columns
$assets = Asset::select('id', 'name', 'tag')->get();

// Use chunk for large datasets
Asset::chunk(100, function ($assets) {
    foreach ($assets as $asset) {
        // Process
    }
});

// Use indexes
Schema::table('assets', function (Blueprint $table) {
    $table->index('clientid');
    $table->index('tag');
});
```

### Caching
```php
// Cache query results
$clients = Cache::remember('clients', 3600, function () {
    return Client::all();
});

// Cache config
php artisan config:cache

// Use Redis for sessions/cache
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### Queue
```php
// Queue heavy tasks
Mail::to($user)->queue(new TicketNotification($ticket));

// Use horizon for queue monitoring
composer require laravel/horizon
php artisan horizon:install
```

---

## Security Checklist

- [ ] Use bcrypt for passwords
- [ ] Enable CSRF protection (Laravel default)
- [ ] Validate all inputs
- [ ] Use prepared statements (Eloquent default)
- [ ] Escape output (Blade default)
- [ ] Use HTTPS in production
- [ ] Set secure session cookies
- [ ] Implement rate limiting
- [ ] Keep Laravel updated
- [ ] Use environment variables for secrets
- [ ] Enable SQL query logging (dev only)
- [ ] Implement audit trail
- [ ] Use Laravel Sanctum for API
- [ ] Implement 2FA (optional)

---

## Resources

### Documentation
- Laravel: https://laravel.com/docs
- Livewire: https://livewire.laravel.com/docs
- PHP: https://www.php.net/docs.php

### Learning
- Laracasts: https://laracasts.com
- Laravel Daily: https://laraveldaily.com
- Laravel News: https://laravel-news.com

### Community
- Discord: https://discord.gg/laravel
- Forum: https://laracasts.com/discuss
- Reddit: r/laravel

### Tools
- Laravel Debugbar: barryvdh/laravel-debugbar
- Laravel IDE Helper: barryvdh/laravel-ide-helper
- Laravel Telescope: laravel/telescope

---

**Keep this file handy during development!** 📌

---

**Version:** 1.0  
**Last Updated:** 2025-11-22
