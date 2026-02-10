# Implementation Plan: Unit Testing for `App\Livewire\Brand\Create` with Pest

This plan outlines the testing strategy for the `Create` brand module in the Livewire component.

## 1. Test Objective
The goal is to achieve 100% code coverage for the `App\Livewire\Brand\Create` component, ensuring all logic paths, validation rules, and authorization checks are verified.

## 2. Test Scenarios

### 2.1 Authorization
- **Scenario**: Guest user access.
  - **Expectation**: Redirected to login or 403 (depending on middleware).
- **Scenario**: User without `add-brand` permission.
  - **Expectation**: `403 Forbidden` response.
- **Scenario**: User with `add-brand` permission.
  - **Expectation**: Component renders successfully (Status code 200).

### 2.2 Initial State & Mounting
- **Scenario**: Breadcrumb event dispatch.
  - **Expectation**: Verify `breadcrumbList` event is dispatched with correct data.
- **Scenario**: Dropdown data population.
  - **Expectation**: Verify `countries`, `states`, and `cities` properties are populated via `Helper` methods.

### 2.3 Validation Rules (Edge Cases)
- **Required Fields**: Verify validation fails when mandatory fields are empty.
  - Fields: `name`, `bob`, `description`, `country_id`, `state_id`, `city_id`, `status`.
- **Max Length**:
  - `name`: > 191 characters.
  - `description`: > 500 characters.
- **Date Format**:
  - `bob`: Invalid format (e.g., '10-02-2024') vs required 'Y-m-d H:i:s'.
- **Relationship Existence**:
  - `country_id`, `state_id`, `city_id`: Non-existent IDs in the database.
- **In-list Validation**:
  - `status`: Values other than 'Y' or 'N'.

### 2.4 Functional Logic (Happy Path)
- **Scenario**: Successful brand creation.
  - **Actions**: Provide valid data for all fields.
  - **Expectations**:
    - Record is created in the `brands` database table.
    - Success flash message is set in the session.
    - Redirected to `/brand` listing page.
    - `wire:navigate` property is used for redirection.

## 3. Technical Approach
- **Framework**: Pest PHP with Livewire Testing utilities.
- **Database**: Use `RefreshDatabase` trait to ensure a clean state.
- **Data Generation**: Use Laravel Factories for `Country`, `State`, and `City`.
- **Mocking/Gates**: Use `Gate::define` or `Gate::authorize` to control access in tests.

## 4. Execution Steps
1. Create `tests/Feature/Livewire/Brand/CreateTest.php`.
2. Define `beforeEach` for user authentication and authorization setup.
3. Implement authorization tests.
4. Implement validation tests using `@dataProvider` or iterative approaches for edge cases.
5. Implement the happy path `store()` test.
6. Run tests with coverage report to verify completion.
