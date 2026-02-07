# How to Use the Reusable Transaction Modals

## Quick Start

To add transaction entry functionality to any page, simply include the modal partials and push the mobile actions:

```blade
@extends('layouts.app')

@section('content')
    <!-- Your page content here -->
    
    <!-- Add buttons to trigger the modals -->
    <button onclick="openModal('cashInModal')">Add Cash In</button>
    <button onclick="openModal('cashOutModal')">Add Cash Out</button>
@endsection

<!-- Include the modals (required) -->
@push('transaction-modals')
    @include('partials.cash-in-modal')
    @include('partials.cash-out-modal')
@endpush

<!-- Optional: Add sticky mobile buttons -->
@push('mobile-actions')
    <div class="md:hidden mobile-sticky-actions">
        <div class="flex gap-3 max-w-[1200px] mx-auto">
            <button onclick="openModal('cashInModal')"
                class="flex-1 flex items-center justify-center gap-2 h-14 bg-success-muted text-white rounded-xl font-bold shadow-lg">
                <i class="ti ti-circle-plus text-xl"></i>
                <span>Cash In</span>
            </button>
            <button onclick="openModal('cashOutModal')"
                class="flex-1 flex items-center justify-center gap-2 h-14 bg-danger-muted text-white rounded-xl font-bold shadow-lg">
                <i class="ti ti-circle-minus text-xl"></i>
                <span>Cash Out</span>
            </button>
        </div>
    </div>
@endpush
```

## File Location

The modal partials are located at:
- `resources/views/partials/cash-in-modal.blade.php`
- `resources/views/partials/cash-out-modal.blade.php`

## Requirements

The modals require the following variable to be available:
- `$activeChapter` - The current active Chapter model instance

This is automatically provided by:
- Dashboard controller (`DashboardController@index`)
- Transactions controller (`TransactionController@index`)

If using in a different controller, make sure to pass the active chapter:

```php
public function yourMethod()
{
    $activeChapter = auth()->user()->chapters()->where('id', session('active_chapter_id'))->first();
    
    return view('your.view', compact('activeChapter'));
}
```

## Modal Features

### Cash In Modal (`partials.cash-in-modal`)
- **Fields:**
  - Amount (required, numeric, step 0.01)
  - Description (required, text)
  - Category (optional, text)
  - Date (required, date, defaults to today)
  - Notes (optional, textarea)
- **Submission:** POST to `route('transactions.store', $activeChapter)` with `type=in`

### Cash Out Modal (`partials.cash-out-modal`)
- **Fields:**
  - Amount (required, numeric, step 0.01)
  - Description (required, text)
  - Category (optional, text)
  - Date (required, date, defaults to today)
  - Notes (optional, textarea)
- **Submission:** POST to `route('transactions.store', $activeChapter)` with `type=out`

## JavaScript Functions

The modals use these global functions (defined in `layouts/app.blade.php`):

```javascript
openModal(modalId)   // Opens a modal by ID
closeModal(modalId)  // Closes a modal by ID
```

Example usage:
```html
<button onclick="openModal('cashInModal')">Add Income</button>
<button onclick="openModal('cashOutModal')">Add Expense</button>
```

## Styling

Both modals include:
- Dark mode support
- Responsive design
- Auto-focus on amount field when opened
- Icon indicators (green for in, red for out)
- Backdrop blur effect
- Smooth animations

## Customization

If you need to customize the modals for a specific page:

1. **Option A:** Copy the partial and modify it
   ```blade
   @include('partials.custom-cash-in-modal')
   ```

2. **Option B:** Pass additional variables to the partial
   ```blade
   @include('partials.cash-in-modal', ['customClass' => 'your-class'])
   ```

3. **Option C:** Override specific parts using Blade sections
   (Not currently implemented, but could be added if needed)

## Examples

### Example 1: Simple Page with Modals

```blade
@extends('layouts.app')

@section('content')
    <h1>My Finances</h1>
    
    <button onclick="openModal('cashInModal')">Record Income</button>
    <button onclick="openModal('cashOutModal')">Record Expense</button>
@endsection

@push('transaction-modals')
    @include('partials.cash-in-modal')
    @include('partials.cash-out-modal')
@endpush
```

### Example 2: With Sticky Mobile Buttons

```blade
@extends('layouts.app')

@section('content')
    <h1>Transaction History</h1>
    
    <!-- Desktop buttons -->
    <div class="hidden md:flex gap-3">
        <button onclick="openModal('cashInModal')">Add Cash In</button>
        <button onclick="openModal('cashOutModal')">Add Cash Out</button>
    </div>
    
    <!-- Your content here -->
@endsection

@push('transaction-modals')
    @include('partials.cash-in-modal')
    @include('partials.cash-out-modal')
@endpush

@push('mobile-actions')
    <div class="md:hidden mobile-sticky-actions">
        <div class="flex gap-3">
            <button onclick="openModal('cashInModal')" class="flex-1 ...">
                Cash In
            </button>
            <button onclick="openModal('cashOutModal')" class="flex-1 ...">
                Cash Out
            </button>
        </div>
    </div>
@endpush
```

## Troubleshooting

### Modal doesn't open
- Check that JavaScript functions are loaded (defined in layout)
- Verify modal ID matches the one in the partial
- Check browser console for errors

### Form doesn't submit
- Ensure `$activeChapter` variable is available
- Verify route exists: `php artisan route:list | grep transactions.store`
- Check CSRF token is present in the form

### Styling looks broken
- Clear view cache: `php artisan view:clear`
- Check Tailwind classes are being loaded
- Verify dark mode classes if using dark theme

## Notes

- The modals are included via `@push` to avoid duplicate HTML
- The layout file handles the `@stack` rendering
- All forms include CSRF protection automatically
- Date fields default to today's date using PHP `date('Y-m-d')`
