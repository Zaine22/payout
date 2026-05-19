# Role-Based Access Control Documentation

## Overview
This application implements role-based access control (RBAC) for Filament resources. Each user role has specific permissions to access different resources.

## User Roles
The application supports four user roles defined in `app/Enums/UserRole.php`:
- **Admin**: Full access to all resources
- **Ezgo**: Access to Ezgo transactions only
- **Ezwel**: Access to Payments resource (view only)
- **Merchant**: Access to Payments resource (view only)

## Resource Access Matrix

| Resource | Admin | Ezgo | Ezwel | Merchant |
|----------|-------|------|-------|----------|
| **Users** | Full Access | No Access | No Access | No Access |
| **Payments** | Full Access | No Access | View Only | View Only |
| **Ezgo Transactions** | Full Access | Full Access | No Access | No Access |

## Detailed Permissions

### Users Resource (`app/Filament/Resources/Users/UserResource.php`)
- **Navigation**: Only visible to Admin
- **View**: Admin only
- **Create**: Admin only
- **Edit**: Admin only
- **Delete**: Admin only

### Payments Resource (`app/Filament/Resources/Payments/PaymentResource.php`)
- **Navigation**: Visible to Admin, Ezwel, and Merchant
- **View**: Admin, Ezwel, and Merchant can view
- **Create**: Admin only
- **Edit**: Admin only
- **Delete**: Admin only

### Ezgo Resource (`app/Filament/Resources/Ezgo/EzgoResource.php`)
- **Navigation**: Visible to Admin and Ezgo role
- **View**: Admin and Ezgo can view
- **Create**: Admin and Ezgo can create
- **Edit**: Admin and Ezgo can edit
- **Delete**: Admin and Ezgo can delete

## Implementation Details

### User Model Methods
The `User` model (`app/Models/User.php`) provides helper methods for role checking:

```php
// Check if user is admin
$user->isAdmin(): bool

// Check if user has a specific role
$user->hasRole(UserRole $role): bool

// Check if user has any of the given roles
$user->hasAnyRole(array $roles): bool
```

### Resource Permission Methods
Each Filament resource implements the following methods for access control:

```php
// Control navigation visibility
public static function shouldRegisterNavigation(): bool

// Control list/index page access
public static function canViewAny(): bool

// Control create action
public static function canCreate(): bool

// Control edit action
public static function canEdit(Model $record): bool

// Control delete action
public static function canDelete(Model $record): bool
```

## How to Add New Resources with Role-Based Access

When creating a new Filament resource, implement the permission methods:

```php
public static function shouldRegisterNavigation(): bool
{
    $user = auth()->user();
    
    // Example: Only Admin and SpecificRole can see this resource
    return $user?->isAdmin() || $user?->hasRole(UserRole::SpecificRole);
}

public static function canViewAny(): bool
{
    $user = auth()->user();
    
    return $user?->isAdmin() || $user?->hasRole(UserRole::SpecificRole);
}

public static function canCreate(): bool
{
    $user = auth()->user();
    
    // Example: Only Admin can create
    return $user?->isAdmin();
}

public static function canEdit(Model $record): bool
{
    $user = auth()->user();
    
    // Example: Only Admin can edit
    return $user?->isAdmin();
}

public static function canDelete(Model $record): bool
{
    $user = auth()->user();
    
    // Example: Only Admin can delete
    return $user?->isAdmin();
}
```

## Panel Access Control

The `User` model also controls panel access via the `canAccessPanel()` method:

```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->hasAnyRole([
        UserRole::Admin,
        UserRole::Ezgo,
        UserRole::Ezwel,
        UserRole::Merchant,
    ]);
}
```

All four roles can access the admin panel, but their visible resources are restricted based on the permissions defined in each resource.

## Testing Role-Based Access

To test the role-based access control:

1. Create users with different roles in the database
2. Log in with each user
3. Verify that:
   - Navigation only shows permitted resources
   - Direct URL access to restricted resources is blocked
   - Action buttons (Create, Edit, Delete) are only visible when permitted

## Security Notes

- All permission checks use the authenticated user via `auth()->user()`
- Permissions are checked at multiple levels: navigation, viewing, and actions
- Direct URL access is protected by the `canViewAny()` method
- Individual record actions are protected by `canEdit()` and `canDelete()` methods
