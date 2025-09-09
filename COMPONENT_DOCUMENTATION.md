# Component Documentation

This document provides comprehensive documentation for all UI components in the Q-Shorten application.

## Design System Overview

Our design system follows atomic design principles with three main categories:
- **Atoms**: Basic building blocks (Button, Input, Badge, etc.)
- **Molecules**: Simple combinations of atoms (Card, Alert, etc.)
- **Organisms**: Complex components (Layout, Navigation, etc.)

## UI Components

### Atoms

#### Button Component
**Location**: `resources/js/Components/UI/Button.vue`

**Props**:
- `variant` (String): Button style variant
  - Options: `primary`, `secondary`, `danger`, `ghost`, `link`
  - Default: `primary`
- `size` (String): Button size
  - Options: `sm`, `md`, `lg`
  - Default: `md`
- `disabled` (Boolean): Disable button
  - Default: `false`
- `loading` (Boolean): Show loading state
  - Default: `false`
- `type` (String): HTML button type
  - Default: `button`

**Usage**:
```vue
<Button variant="primary" size="md" @click="handleClick">
  Click me
</Button>

<Button variant="danger" :loading="isSubmitting">
  Delete
</Button>
```

**Features**:
- Full accessibility support with ARIA attributes
- Dark mode support
- Loading state with spinner
- Keyboard navigation
- Focus management

#### Input Component
**Location**: `resources/js/Components/UI/Input.vue`

**Props**:
- `modelValue` (String): Input value (v-model)
- `type` (String): Input type
  - Default: `text`
- `placeholder` (String): Placeholder text
- `disabled` (Boolean): Disable input
  - Default: `false`
- `error` (String): Error message
- `label` (String): Input label
- `required` (Boolean): Required field
  - Default: `false`

**Usage**:
```vue
<Input 
  v-model="form.email" 
  type="email" 
  label="Email Address"
  placeholder="Enter your email"
  :error="errors.email"
  required
/>
```

**Features**:
- Two-way data binding with v-model
- Error state styling
- Accessibility labels
- Dark mode support
- Focus management

#### Badge Component
**Location**: `resources/js/Components/UI/Badge.vue`

**Props**:
- `variant` (String): Badge style variant
  - Options: `default`, `success`, `warning`, `danger`, `info`
  - Default: `default`
- `size` (String): Badge size
  - Options: `sm`, `md`, `lg`
  - Default: `md`

**Usage**:
```vue
<Badge variant="success">Active</Badge>
<Badge variant="danger" size="sm">Error</Badge>
```

### Molecules

#### Card Component
**Location**: `resources/js/Components/UI/Card.vue`

**Props**:
- `padding` (String): Card padding size
  - Options: `sm`, `md`, `lg`
  - Default: `md`
- `shadow` (String): Card shadow intensity
  - Options: `none`, `sm`, `md`, `lg`
  - Default: `md`

**Slots**:
- `header`: Card header content
- `default`: Card body content
- `footer`: Card footer content

**Usage**:
```vue
<Card>
  <template #header>
    <h3>Card Title</h3>
  </template>
  
  <p>Card content goes here</p>
  
  <template #footer>
    <Button>Action</Button>
  </template>
</Card>
```

#### Alert Component
**Location**: `resources/js/Components/UI/Alert.vue`

**Props**:
- `type` (String): Alert type
  - Options: `success`, `error`, `warning`, `info`
  - Default: `info`
- `dismissible` (Boolean): Show dismiss button
  - Default: `false`
- `title` (String): Alert title

**Events**:
- `dismiss`: Emitted when alert is dismissed

**Usage**:
```vue
<Alert type="success" title="Success!" dismissible @dismiss="handleDismiss">
  Your action was completed successfully.
</Alert>
```

#### Modal Component
**Location**: `resources/js/Components/UI/Modal.vue`

**Props**:
- `show` (Boolean): Show/hide modal
  - Default: `false`
- `maxWidth` (String): Maximum modal width
  - Options: `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `4xl`, `5xl`, `6xl`
  - Default: `2xl`
- `closeable` (Boolean): Allow closing modal
  - Default: `true`
- `title` (String): Modal title
- `persistent` (Boolean): Prevent closing on backdrop click
  - Default: `false`

**Events**:
- `close`: Emitted when modal should close
- `opened`: Emitted when modal opens
- `closed`: Emitted when modal closes

**Slots**:
- `header`: Custom header content
- `default`: Modal body content
- `footer`: Modal footer content

**Usage**:
```vue
<Modal :show="showModal" title="Confirm Action" @close="showModal = false">
  <p>Are you sure you want to proceed?</p>
  
  <template #footer>
    <Button variant="secondary" @click="showModal = false">Cancel</Button>
    <Button variant="danger" @click="confirmAction">Confirm</Button>
  </template>
</Modal>
```

#### Dropdown Component
**Location**: `resources/js/Components/UI/Dropdown.vue`

**Props**:
- `align` (String): Dropdown alignment
  - Options: `left`, `right`
  - Default: `right`
- `width` (String): Dropdown width
  - Options: `48`, `56`, `64`, `72`, `80`, `96`
  - Default: `48`
- `contentClasses` (String): Custom content classes
  - Default: `py-1 bg-white dark:bg-gray-800`

**Slots**:
- `trigger`: Dropdown trigger element
- `default`: Dropdown content

**Usage**:
```vue
<Dropdown align="right" width="56">
  <template #trigger>
    <Button variant="ghost">Options</Button>
  </template>
  
  <DropdownLink href="/profile">Profile</DropdownLink>
  <DropdownLink href="/settings">Settings</DropdownLink>
</Dropdown>
```

#### DropdownLink Component
**Location**: `resources/js/Components/UI/DropdownLink.vue`

**Props**:
- `href` (String): Link URL
- `method` (String): HTTP method for form submission
  - Default: `get`
- `as` (String): Render as different element
  - Options: `a`, `button`
  - Default: `a`

**Usage**:
```vue
<DropdownLink href="/logout" method="post">Logout</DropdownLink>
<DropdownLink as="button" @click="handleAction">Custom Action</DropdownLink>
```

### Utility Components

#### Loading Component
**Location**: `resources/js/Components/UI/Loading.vue`

**Props**:
- `size` (String): Spinner size
  - Options: `sm`, `md`, `lg`
  - Default: `md`
- `color` (String): Spinner color
  - Default: `blue`

**Usage**:
```vue
<Loading size="lg" />
```

#### ThemeToggle Component
**Location**: `resources/js/Components/UI/ThemeToggle.vue`

**Features**:
- Toggles between light and dark themes
- Persists theme preference in localStorage
- Accessible with keyboard navigation
- Smooth transitions

**Usage**:
```vue
<ThemeToggle />
```

## Legacy Components

The following components are maintained for backward compatibility:

### ApplicationLogo
**Location**: `resources/js/Components/ApplicationLogo.vue`
- Simple logo component
- Used in authentication pages

### Checkbox
**Location**: `resources/js/Components/Checkbox.vue`
- Basic checkbox input
- Consider migrating to UI/Input with type="checkbox"

### InputError
**Location**: `resources/js/Components/InputError.vue`
- Error message display
- Consider using UI/Input error prop instead

### InputLabel
**Location**: `resources/js/Components/InputLabel.vue`
- Form label component
- Consider using UI/Input label prop instead

### NavLink & ResponsiveNavLink
**Location**: `resources/js/Components/NavLink.vue`, `resources/js/Components/ResponsiveNavLink.vue`
- Navigation link components
- Used in main navigation

## Best Practices

### Component Usage
1. **Always use UI components** from the `UI/` directory for new features
2. **Prefer composition** over creating new components when possible
3. **Follow naming conventions** using PascalCase for component names
4. **Use props validation** to ensure type safety
5. **Implement proper accessibility** with ARIA attributes

### Styling Guidelines
1. **Use Tailwind classes** for styling
2. **Support dark mode** with `dark:` prefixes
3. **Maintain consistent spacing** using Tailwind's spacing scale
4. **Follow color palette** defined in the design system
5. **Ensure responsive design** with mobile-first approach

### Accessibility Requirements
1. **Keyboard navigation** must work for all interactive elements
2. **Screen reader support** with proper ARIA labels
3. **Focus management** with visible focus indicators
4. **Color contrast** meeting WCAG AA standards
5. **Semantic HTML** structure

### Performance Considerations
1. **Use computed properties** for expensive calculations
2. **Implement proper memoization** for complex components
3. **Lazy load** large components when possible
4. **Optimize bundle size** by avoiding unnecessary imports
5. **Use v-show vs v-if** appropriately

## Migration Guide

When migrating from legacy components to UI components:

1. **Replace imports**:
   ```vue
   // Old
   import PrimaryButton from '@/Components/PrimaryButton.vue'
   
   // New
   import Button from '@/Components/UI/Button.vue'
   ```

2. **Update props**:
   ```vue
   <!-- Old -->
   <PrimaryButton>Click me</PrimaryButton>
   
   <!-- New -->
   <Button variant="primary">Click me</Button>
   ```

3. **Check functionality** to ensure all features work as expected
4. **Test accessibility** with screen readers and keyboard navigation
5. **Verify dark mode** support

## Contributing

When adding new components:

1. **Follow atomic design principles**
2. **Add comprehensive prop validation**
3. **Include accessibility features**
4. **Support dark mode**
5. **Write clear documentation**
6. **Add usage examples**
7. **Test thoroughly** across different browsers and devices

For questions or suggestions, please refer to the development team.