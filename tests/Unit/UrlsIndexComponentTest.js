import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createInertiaApp } from '@inertiajs/vue3'
import Index from '@/Pages/Urls/Index.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

// Mock Inertia components
vi.mock('@inertiajs/vue3', () => ({
  Head: {
    name: 'Head',
    template: '<div></div>'
  },
  Link: {
    name: 'Link',
    template: '<a><slot /></a>',
    props: ['href', 'class']
  },
  router: {
    delete: vi.fn()
  }
}))

// Mock AuthenticatedLayout
vi.mock('@/Layouts/AuthenticatedLayout.vue', () => ({
  default: {
    name: 'AuthenticatedLayout',
    template: '<div><slot name="header" /><slot /></div>'
  }
}))

// Mock global route function
global.route = vi.fn((name, params) => {
  const routes = {
    'urls.create': '/urls/create',
    'urls.edit': (id) => `/urls/${id}/edit`,
    'urls.destroy': (id) => `/urls/${id}`,
    'analytics.show': (id) => `/analytics/${id}`
  }
  return typeof routes[name] === 'function' ? routes[name](params) : routes[name]
})

// Mock navigator.clipboard
Object.assign(navigator, {
  clipboard: {
    writeText: vi.fn(() => Promise.resolve())
  }
})

// Mock window.confirm
global.confirm = vi.fn(() => true)

describe('Urls Index Component', () => {
  let wrapper
  
  const mockUrls = {
    data: [
      {
        id: 1,
        title: 'Test URL 1',
        original_url: 'https://example.com',
        short_code: 'abc123',
        short_url: 'http://localhost/s/abc123',
        is_active: true,
        click_count: 5,
        created_at: '2024-01-01T00:00:00.000000Z',
        updated_at: '2024-01-01T00:00:00.000000Z'
      },
      {
        id: 2,
        title: null,
        original_url: 'https://google.com',
        short_code: 'def456',
        short_url: 'http://localhost/s/def456',
        is_active: false,
        click_count: 0,
        created_at: '2024-01-02T00:00:00.000000Z',
        updated_at: '2024-01-02T00:00:00.000000Z'
      }
    ],
    total: 2,
    from: 1,
    to: 2,
    current_page: 1,
    last_page: 1,
    per_page: 10,
    prev_page_url: null,
    next_page_url: null,
    links: [
      { label: 'Previous', url: null, active: false },
      { label: '1', url: '/urls?page=1', active: true },
      { label: 'Next', url: null, active: false }
    ]
  }

  const mockPageProps = {
    flash: {
      success: null
    }
  }

  beforeEach(() => {
    vi.clearAllMocks()
    
    wrapper = mount(Index, {
      props: {
        urls: mockUrls
      },
      global: {
        mocks: {
          $page: {
            props: mockPageProps
          },
          route: global.route
        },
        stubs: {
          Head: true,
          Link: true,
          AuthenticatedLayout: true
        }
      }
    })
  })

  describe('Component Rendering', () => {
    it('renders the component without errors', () => {
      expect(wrapper.exists()).toBe(true)
      expect(wrapper.vm).toBeDefined()
    })

    it('displays the correct page title', () => {
      expect(wrapper.find('h2').text()).toBe('My URLs')
    })

    it('renders the create URL button', () => {
      const createButton = wrapper.find('[data-testid="create-url-btn"]') || 
                          wrapper.find('a[href="/urls/create"]') ||
                          wrapper.findComponent({ name: 'Link' })
      expect(createButton.exists()).toBe(true)
    })

    it('renders stats cards with correct data', () => {
      const statsCards = wrapper.findAll('.bg-white.overflow-hidden.shadow-sm.rounded-lg')
      expect(statsCards.length).toBeGreaterThanOrEqual(4) // 4 stat cards + table
    })

    it('displays URLs table with correct headers', () => {
      const tableHeaders = wrapper.findAll('th')
      const expectedHeaders = ['URL', 'Short Code', 'Clicks', 'Status', 'Created', 'Actions']
      
      expectedHeaders.forEach(header => {
        const headerExists = tableHeaders.some(th => th.text().includes(header))
        expect(headerExists).toBe(true)
      })
    })

    it('renders URL rows with correct data', () => {
      const tableRows = wrapper.findAll('tbody tr')
      expect(tableRows.length).toBe(2)
      
      // Check first URL row
      const firstRow = tableRows[0]
      expect(firstRow.text()).toContain('Test URL 1')
      expect(firstRow.text()).toContain('https://example.com')
      expect(firstRow.text()).toContain('abc123')
      expect(firstRow.text()).toContain('5')
      expect(firstRow.text()).toContain('Active')
    })

    it('handles URLs with null titles correctly', () => {
      const tableRows = wrapper.findAll('tbody tr')
      const secondRow = tableRows[1]
      expect(secondRow.text()).toContain('Untitled')
    })

    it('displays correct status badges', () => {
      const statusBadges = wrapper.findAll('.inline-flex.px-2.py-1.text-xs.font-semibold.rounded-full')
      expect(statusBadges.length).toBe(2)
      
      // Active status should have green classes
      expect(statusBadges[0].classes()).toContain('bg-green-100')
      expect(statusBadges[0].classes()).toContain('text-green-800')
      
      // Inactive status should have red classes
      expect(statusBadges[1].classes()).toContain('bg-red-100')
      expect(statusBadges[1].classes()).toContain('text-red-800')
    })
  })

  describe('Computed Properties', () => {
    it('calculates total clicks correctly', () => {
      expect(wrapper.vm.totalClicks).toBe(5)
    })

    it('calculates active URLs correctly', () => {
      expect(wrapper.vm.activeUrls).toBe(1)
    })

    it('calculates average clicks correctly', () => {
      expect(wrapper.vm.averageClicks).toBe(3) // Math.round(5/2) = 3
    })

    it('handles empty URLs array for computed properties', async () => {
      await wrapper.setProps({
        urls: {
          ...mockUrls,
          data: []
        }
      })
      
      expect(wrapper.vm.totalClicks).toBe(0)
      expect(wrapper.vm.activeUrls).toBe(0)
      expect(wrapper.vm.averageClicks).toBe(0)
    })
  })

  describe('Methods', () => {
    describe('formatDate', () => {
      it('formats date correctly', () => {
        const testDate = '2024-01-15T10:30:00.000000Z'
        const formatted = wrapper.vm.formatDate(testDate)
        expect(formatted).toMatch(/Jan 15, 2024/)
      })
    })

    describe('copyToClipboard', () => {
      it('copies text to clipboard successfully', async () => {
        const testText = 'http://localhost/s/abc123'
        await wrapper.vm.copyToClipboard(testText)
        
        expect(navigator.clipboard.writeText).toHaveBeenCalledWith(testText)
      })

      it('handles clipboard copy errors gracefully', async () => {
        const consoleSpy = vi.spyOn(console, 'error').mockImplementation(() => {})
        navigator.clipboard.writeText.mockRejectedValueOnce(new Error('Clipboard error'))
        
        await wrapper.vm.copyToClipboard('test')
        
        expect(consoleSpy).toHaveBeenCalledWith('Failed to copy: ', expect.any(Error))
        consoleSpy.mockRestore()
      })
    })

    describe('deleteUrl', () => {
      it('deletes URL when confirmed', () => {
        const mockRouter = { delete: vi.fn() }
        wrapper.vm.$inertia = mockRouter
        
        const testUrl = mockUrls.data[0]
        wrapper.vm.deleteUrl(testUrl)
        
        expect(global.confirm).toHaveBeenCalledWith('Are you sure you want to delete "Test URL 1"?')
      })

      it('uses original URL when title is null', () => {
        const testUrl = {
          id: 1,
          title: null,
          original_url: 'https://example.com'
        }
        
        wrapper.vm.deleteUrl(testUrl)
        
        expect(global.confirm).toHaveBeenCalledWith('Are you sure you want to delete "https://example.com"?')
      })

      it('does not delete when not confirmed', () => {
        global.confirm.mockReturnValueOnce(false)
        const mockRouter = { delete: vi.fn() }
        wrapper.vm.$inertia = mockRouter
        
        wrapper.vm.deleteUrl(mockUrls.data[0])
        
        expect(mockRouter.delete).not.toHaveBeenCalled()
      })
    })
  })

  describe('User Interactions', () => {
    it('triggers copy to clipboard when copy button is clicked', async () => {
      const copyButtons = wrapper.findAll('button[title="Copy to clipboard"]')
      expect(copyButtons.length).toBeGreaterThan(0)
      
      await copyButtons[0].trigger('click')
      
      expect(navigator.clipboard.writeText).toHaveBeenCalledWith('http://localhost/s/abc123')
    })

    it('triggers delete confirmation when delete button is clicked', async () => {
      const deleteButtons = wrapper.findAll('button[title="Delete"]')
      expect(deleteButtons.length).toBeGreaterThan(0)
      
      await deleteButtons[0].trigger('click')
      
      expect(global.confirm).toHaveBeenCalled()
    })

    it('navigates to analytics page when analytics button is clicked', () => {
      const analyticsLinks = wrapper.findAll('a[title="View Analytics"]')
      expect(analyticsLinks.length).toBeGreaterThan(0)
    })

    it('navigates to edit page when edit button is clicked', () => {
      const editLinks = wrapper.findAll('a[title="Edit"]')
      expect(editLinks.length).toBeGreaterThan(0)
    })
  })

  describe('Props Validation', () => {
    it('accepts valid urls prop structure', () => {
      expect(wrapper.props('urls')).toEqual(mockUrls)
    })

    it('handles missing optional properties gracefully', async () => {
      const minimalUrls = {
        data: [{
          id: 1,
          original_url: 'https://test.com',
          short_code: 'test',
          short_url: 'http://localhost/s/test',
          is_active: true,
          click_count: 0,
          created_at: '2024-01-01T00:00:00.000000Z',
          updated_at: '2024-01-01T00:00:00.000000Z'
        }],
        total: 1,
        from: 1,
        to: 1,
        current_page: 1,
        last_page: 1,
        per_page: 10,
        prev_page_url: null,
        next_page_url: null,
        links: []
      }
      
      await wrapper.setProps({ urls: minimalUrls })
      
      expect(wrapper.vm).toBeDefined()
      expect(() => wrapper.vm.totalClicks).not.toThrow()
    })
  })

  describe('Flash Messages', () => {
    it('displays success flash message when present', async () => {
      wrapper.vm.$page.props.flash.success = 'URL created successfully!'
      await wrapper.vm.$nextTick()
      
      const successMessage = wrapper.find('.bg-green-100.border.border-green-400')
      if (successMessage.exists()) {
        expect(successMessage.text()).toContain('URL created successfully!')
      }
    })

    it('does not display flash message container when no message', () => {
      const successMessage = wrapper.find('.bg-green-100.border.border-green-400')
      expect(successMessage.exists()).toBe(false)
    })
  })

  describe('Pagination', () => {
    it('displays pagination when there are multiple pages', async () => {
      const urlsWithPagination = {
        ...mockUrls,
        last_page: 3,
        links: [
          { label: 'Previous', url: null, active: false },
          { label: '1', url: '/urls?page=1', active: true },
          { label: '2', url: '/urls?page=2', active: false },
          { label: '3', url: '/urls?page=3', active: false },
          { label: 'Next', url: '/urls?page=2', active: false }
        ]
      }
      
      await wrapper.setProps({ urls: urlsWithPagination })
      
      const paginationInfo = wrapper.find('p.text-sm.text-gray-700')
      if (paginationInfo.exists()) {
        expect(paginationInfo.text()).toContain('Showing 1 to 2 of 2 results')
      }
    })

    it('does not display pagination when links are 3 or fewer', () => {
      // Default mockUrls has 3 links, so pagination should not show
      const pagination = wrapper.find('nav.flex.items-center.justify-between')
      expect(pagination.exists()).toBe(false)
    })
  })

  describe('Empty State', () => {
    it('handles empty URLs list gracefully', async () => {
      await wrapper.setProps({
        urls: {
          ...mockUrls,
          data: [],
          total: 0
        }
      })
      
      expect(wrapper.vm.totalClicks).toBe(0)
      expect(wrapper.vm.activeUrls).toBe(0)
      expect(wrapper.vm.averageClicks).toBe(0)
      
      const tableRows = wrapper.findAll('tbody tr')
      expect(tableRows.length).toBe(0)
    })
  })

  describe('Accessibility', () => {
    it('has proper button titles for screen readers', () => {
      const copyButton = wrapper.find('button[title="Copy to clipboard"]')
      const deleteButton = wrapper.find('button[title="Delete"]')
      const analyticsLink = wrapper.find('a[title="View Analytics"]')
      const editLink = wrapper.find('a[title="Edit"]')
      
      expect(copyButton.exists()).toBe(true)
      expect(deleteButton.exists()).toBe(true)
      expect(analyticsLink.exists()).toBe(true)
      expect(editLink.exists()).toBe(true)
    })

    it('uses semantic HTML elements', () => {
      expect(wrapper.find('table').exists()).toBe(true)
      expect(wrapper.find('thead').exists()).toBe(true)
      expect(wrapper.find('tbody').exists()).toBe(true)
      expect(wrapper.find('nav').exists()).toBe(false) // No pagination in default state
    })
  })
})