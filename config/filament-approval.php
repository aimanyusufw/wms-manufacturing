<?php

use App\Models\User;
use Wezlo\FilamentApproval\ApproverResolvers\CallbackResolver;
use Wezlo\FilamentApproval\ApproverResolvers\RoleResolver;
use Wezlo\FilamentApproval\ApproverResolvers\UserResolver;

return [

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    */
    'user_model' => User::class,

    /*
    |--------------------------------------------------------------------------
    | Approver Resolvers
    |--------------------------------------------------------------------------
    | Registered resolver classes available in the flow builder UI.
    */
    'approver_resolvers' => [
        UserResolver::class,
        RoleResolver::class,
        CallbackResolver::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy
    |--------------------------------------------------------------------------
    | Enable multi-tenancy to scope approval flows and approvers per tenant.
    | When enabled, the tenant_column is used on the approval_flows table
    | and on models/users to isolate approvals per tenant.
    |
    | Set column to match your application's tenant foreign key
    | (e.g. 'company_id', 'team_id', 'organization_id').
    |
    | scope_approvers: When true, role-based resolvers will also filter
    | users by the tenant column.
    */
    'multi_tenancy' => [
        'enabled' => false,
        'column' => 'company_id',
        'scope_approvers' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | SLA Warning Threshold
    |--------------------------------------------------------------------------
    | Fraction of SLA time elapsed before sending a warning (0.75 = 75%).
    */
    'sla_warning_threshold' => 0.75,

    /*
    |--------------------------------------------------------------------------
    | Auto-register SLA Command Schedule
    |--------------------------------------------------------------------------
    | When true, the package registers `approval:process-sla` to run every minute.
    */
    'schedule_sla_command' => true,

    /*
    |--------------------------------------------------------------------------
    | Navigation Group
    |--------------------------------------------------------------------------
    */
    'navigation_group' => 'Approvals',

    /*
    |--------------------------------------------------------------------------
    | Database Table Prefix
    |--------------------------------------------------------------------------
    | Prefix for all package tables.
    */
    'table_prefix' => '',

];
