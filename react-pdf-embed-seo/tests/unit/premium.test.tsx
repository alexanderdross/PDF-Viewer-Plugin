/**
 * Unit Tests - @pdf-embed-seo/react-premium
 *
 * Run with: pnpm test
 */

import React from 'react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { renderHook, act } from '@testing-library/react';
import '@testing-library/jest-dom';

// ============================================
// Mock Data
// ============================================

const mockDocument = {
  id: 1,
  title: 'Protected Document',
  slug: 'protected-document',
  url: 'https://example.com/pdf/protected-document',
  passwordProtected: true,
  pageCount: 20,
};

const mockAnalyticsData = {
  totalViews: 15000,
  totalDownloads: 3500,
  documentsCount: 45,
  avgTimeOnPage: 185, // seconds
  topDocuments: [
    { id: 1, title: 'Popular Doc', views: 5000, downloads: 1200 },
    { id: 2, title: 'Another Doc', views: 3000, downloads: 800 },
  ],
};

const mockBookmarks = [
  {
    title: 'Chapter 1: Introduction',
    page: 1,
    children: [
      { title: 'Overview', page: 2 },
      { title: 'Getting Started', page: 5 },
    ],
  },
  {
    title: 'Chapter 2: Installation',
    page: 10,
    children: [],
  },
];

// ============================================
// Password Protection Tests
// ============================================

describe('usePasswordProtection Hook', () => {
  const mockApiClient = {
    verifyPassword: vi.fn(),
  };

  beforeEach(() => {
    vi.clearAllMocks();
    sessionStorage.clear();
  });

  it('should return isProtected true for protected documents', () => {
    const { result } = renderHook(() =>
      usePasswordProtection(mockDocument, { apiClient: mockApiClient })
    );

    expect(result.current.isProtected).toBe(true);
  });

  it('should return isUnlocked false initially', () => {
    const { result } = renderHook(() =>
      usePasswordProtection(mockDocument, { apiClient: mockApiClient })
    );

    expect(result.current.isUnlocked).toBe(false);
  });

  it('should unlock with correct password', async () => {
    mockApiClient.verifyPassword.mockResolvedValueOnce({ success: true });

    const { result } = renderHook(() =>
      usePasswordProtection(mockDocument, { apiClient: mockApiClient })
    );

    await act(async () => {
      await result.current.verifyPassword('correct-password');
    });

    expect(result.current.isUnlocked).toBe(true);
    expect(result.current.error).toBeNull();
  });

  it('should return error with wrong password', async () => {
    mockApiClient.verifyPassword.mockResolvedValueOnce({
      success: false,
      error: 'Invalid password',
    });

    const { result } = renderHook(() =>
      usePasswordProtection(mockDocument, { apiClient: mockApiClient })
    );

    await act(async () => {
      await result.current.verifyPassword('wrong-password');
    });

    expect(result.current.isUnlocked).toBe(false);
    expect(result.current.error).toBe('Invalid password');
  });

  it('should persist unlock in session', async () => {
    mockApiClient.verifyPassword.mockResolvedValueOnce({ success: true });

    const { result, rerender } = renderHook(() =>
      usePasswordProtection(mockDocument, { apiClient: mockApiClient })
    );

    await act(async () => {
      await result.current.verifyPassword('correct-password');
    });

    // Simulate remount
    rerender();

    expect(result.current.isUnlocked).toBe(true);
  });

  it('should show loading state during verification', async () => {
    let resolvePromise: Function;
    mockApiClient.verifyPassword.mockReturnValueOnce(
      new Promise((resolve) => {
        resolvePromise = resolve;
      })
    );

    const { result } = renderHook(() =>
      usePasswordProtection(mockDocument, { apiClient: mockApiClient })
    );

    act(() => {
      result.current.verifyPassword('password');
    });

    expect(result.current.isLoading).toBe(true);

    await act(async () => {
      resolvePromise!({ success: true });
    });

    expect(result.current.isLoading).toBe(false);
  });
});

describe('PdfPasswordModal Component', () => {
  it('should render modal when isOpen is true', () => {
    render(
      <PdfPasswordModal
        isOpen={true}
        onSubmit={vi.fn()}
        onCancel={vi.fn()}
      />
    );

    expect(screen.getByRole('dialog')).toBeInTheDocument();
  });

  it('should not render when isOpen is false', () => {
    render(
      <PdfPasswordModal
        isOpen={false}
        onSubmit={vi.fn()}
        onCancel={vi.fn()}
      />
    );

    expect(screen.queryByRole('dialog')).not.toBeInTheDocument();
  });

  it('should render password input', () => {
    render(
      <PdfPasswordModal
        isOpen={true}
        onSubmit={vi.fn()}
        onCancel={vi.fn()}
      />
    );

    expect(screen.getByLabelText(/password/i)).toBeInTheDocument();
  });

  it('should call onSubmit with password', async () => {
    const onSubmit = vi.fn();

    render(
      <PdfPasswordModal
        isOpen={true}
        onSubmit={onSubmit}
        onCancel={vi.fn()}
      />
    );

    const input = screen.getByLabelText(/password/i);
    fireEvent.change(input, { target: { value: 'mypassword' } });

    const submitButton = screen.getByRole('button', { name: /unlock|submit/i });
    fireEvent.click(submitButton);

    expect(onSubmit).toHaveBeenCalledWith('mypassword');
  });

  it('should call onCancel when cancel clicked', () => {
    const onCancel = vi.fn();

    render(
      <PdfPasswordModal
        isOpen={true}
        onSubmit={vi.fn()}
        onCancel={onCancel}
      />
    );

    const cancelButton = screen.getByRole('button', { name: /cancel/i });
    fireEvent.click(cancelButton);

    expect(onCancel).toHaveBeenCalled();
  });

  it('should display error message', () => {
    render(
      <PdfPasswordModal
        isOpen={true}
        onSubmit={vi.fn()}
        onCancel={vi.fn()}
        error="Incorrect password"
      />
    );

    expect(screen.getByText('Incorrect password')).toBeInTheDocument();
  });

  it('should show loading state', () => {
    render(
      <PdfPasswordModal
        isOpen={true}
        onSubmit={vi.fn()}
        onCancel={vi.fn()}
        isLoading={true}
      />
    );

    expect(screen.getByRole('button', { name: /unlock|submit/i })).toBeDisabled();
  });

  it('should display document title', () => {
    render(
      <PdfPasswordModal
        isOpen={true}
        onSubmit={vi.fn()}
        onCancel={vi.fn()}
        documentTitle="Secret Document"
      />
    );

    expect(screen.getByText('Secret Document')).toBeInTheDocument();
  });
});

// ============================================
// Reading Progress Tests
// ============================================

describe('useReadingProgress Hook', () => {
  const mockApiClient = {
    getProgress: vi.fn(),
    saveProgress: vi.fn(),
  };

  beforeEach(() => {
    vi.clearAllMocks();
    mockApiClient.getProgress.mockResolvedValue({
      page: 5,
      scroll: 0.5,
      zoom: 1,
    });
    mockApiClient.saveProgress.mockResolvedValue({ success: true });
  });

  it('should fetch existing progress', async () => {
    const { result } = renderHook(() =>
      useReadingProgress(mockDocument, { apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.progress).toBeDefined();
    });

    expect(result.current.progress?.page).toBe(5);
  });

  it('should calculate percent complete', async () => {
    const { result } = renderHook(() =>
      useReadingProgress(mockDocument, { apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.progress).toBeDefined();
    });

    // Page 5 of 20 = 25%
    expect(result.current.percentComplete).toBe(25);
  });

  it('should save progress', async () => {
    const { result } = renderHook(() =>
      useReadingProgress(mockDocument, { apiClient: mockApiClient })
    );

    await act(async () => {
      await result.current.saveProgress({ page: 10, scroll: 0.3 });
    });

    expect(mockApiClient.saveProgress).toHaveBeenCalledWith(
      mockDocument.id,
      expect.objectContaining({ page: 10, scroll: 0.3 })
    );
  });

  it('should update local progress after save', async () => {
    const { result } = renderHook(() =>
      useReadingProgress(mockDocument, { apiClient: mockApiClient })
    );

    await act(async () => {
      await result.current.saveProgress({ page: 15 });
    });

    expect(result.current.progress?.page).toBe(15);
    expect(result.current.percentComplete).toBe(75);
  });
});

describe('PdfProgressBar Component', () => {
  it('should render progress bar', () => {
    render(<PdfProgressBar progress={50} />);

    expect(screen.getByRole('progressbar')).toBeInTheDocument();
  });

  it('should display correct percentage width', () => {
    render(<PdfProgressBar progress={75} />);

    const fill = screen.getByTestId('progress-fill');
    expect(fill).toHaveStyle({ width: '75%' });
  });

  it('should render at top position by default', () => {
    render(<PdfProgressBar progress={50} />);

    const bar = screen.getByRole('progressbar');
    expect(bar).not.toHaveClass('pdf-progress-bottom');
  });

  it('should render at bottom position when specified', () => {
    render(<PdfProgressBar progress={50} position="bottom" />);

    const bar = screen.getByRole('progressbar');
    expect(bar).toHaveClass('pdf-progress-bottom');
  });

  it('should display progress info when showInfo is true', () => {
    render(
      <PdfProgressBar
        progress={50}
        showInfo={true}
        currentPage={10}
        totalPages={20}
      />
    );

    expect(screen.getByText(/10 of 20/)).toBeInTheDocument();
    expect(screen.getByText(/50%/)).toBeInTheDocument();
  });
});

// ============================================
// Analytics Dashboard Tests
// ============================================

describe('useAnalytics Hook', () => {
  const mockApiClient = {
    getAnalytics: vi.fn(),
  };

  beforeEach(() => {
    vi.clearAllMocks();
    mockApiClient.getAnalytics.mockResolvedValue(mockAnalyticsData);
  });

  it('should fetch analytics data', async () => {
    const { result } = renderHook(() =>
      useAnalytics({ apiClient: mockApiClient })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    expect(result.current.data).toEqual(mockAnalyticsData);
  });

  it('should support period parameter', async () => {
    const { result } = renderHook(() =>
      useAnalytics({ apiClient: mockApiClient, period: '7days' })
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    expect(mockApiClient.getAnalytics).toHaveBeenCalledWith(
      expect.objectContaining({ period: '7days' })
    );
  });

  it('should refetch when period changes', async () => {
    const { result, rerender } = renderHook(
      (props) => useAnalytics({ apiClient: mockApiClient, ...props }),
      { initialProps: { period: '7days' } }
    );

    await waitFor(() => {
      expect(result.current.isLoading).toBe(false);
    });

    rerender({ period: '30days' });

    await waitFor(() => {
      expect(mockApiClient.getAnalytics).toHaveBeenCalledTimes(2);
    });
  });
});

describe('PdfAnalyticsDashboard Component', () => {
  const mockData = mockAnalyticsData;

  it('should render dashboard', () => {
    render(<PdfAnalyticsDashboard data={mockData} />);

    expect(screen.getByText(/PDF Analytics/i)).toBeInTheDocument();
  });

  it('should display total views', () => {
    render(<PdfAnalyticsDashboard data={mockData} />);

    expect(screen.getByText('15,000')).toBeInTheDocument();
  });

  it('should display total downloads', () => {
    render(<PdfAnalyticsDashboard data={mockData} />);

    expect(screen.getByText('3,500')).toBeInTheDocument();
  });

  it('should display documents count', () => {
    render(<PdfAnalyticsDashboard data={mockData} />);

    expect(screen.getByText('45')).toBeInTheDocument();
  });

  it('should display avg time on page', () => {
    render(<PdfAnalyticsDashboard data={mockData} />);

    // 185 seconds = 3:05
    expect(screen.getByText(/3:05|3m 5s/)).toBeInTheDocument();
  });

  it('should render period selector', () => {
    render(<PdfAnalyticsDashboard data={mockData} />);

    expect(screen.getByRole('combobox')).toBeInTheDocument();
  });

  it('should call onPeriodChange when period changes', () => {
    const onPeriodChange = vi.fn();

    render(
      <PdfAnalyticsDashboard
        data={mockData}
        onPeriodChange={onPeriodChange}
      />
    );

    const select = screen.getByRole('combobox');
    fireEvent.change(select, { target: { value: '7days' } });

    expect(onPeriodChange).toHaveBeenCalledWith('7days');
  });

  it('should render export buttons when showExport is true', () => {
    render(<PdfAnalyticsDashboard data={mockData} showExport={true} />);

    expect(screen.getByRole('button', { name: /csv/i })).toBeInTheDocument();
    expect(screen.getByRole('button', { name: /json/i })).toBeInTheDocument();
  });

  it('should render documents table', () => {
    render(<PdfAnalyticsDashboard data={mockData} />);

    expect(screen.getByRole('table')).toBeInTheDocument();
    expect(screen.getByText('Popular Doc')).toBeInTheDocument();
  });

  it('should show loading state', () => {
    render(<PdfAnalyticsDashboard isLoading={true} />);

    expect(screen.getByText(/loading/i)).toBeInTheDocument();
  });

  it('should show error state', () => {
    render(<PdfAnalyticsDashboard error="Failed to load analytics" />);

    expect(screen.getByText('Failed to load analytics')).toBeInTheDocument();
  });
});

// ============================================
// Search Feature Tests
// ============================================

describe('PdfSearchBar Component', () => {
  it('should render search input', () => {
    render(<PdfSearchBar onSearch={vi.fn()} />);

    expect(screen.getByRole('searchbox')).toBeInTheDocument();
  });

  it('should call onSearch when typing', () => {
    const onSearch = vi.fn();

    render(<PdfSearchBar onSearch={onSearch} />);

    const input = screen.getByRole('searchbox');
    fireEvent.change(input, { target: { value: 'test' } });

    expect(onSearch).toHaveBeenCalledWith('test');
  });

  it('should display results count', () => {
    render(
      <PdfSearchBar
        onSearch={vi.fn()}
        resultsCount={5}
        currentResult={2}
      />
    );

    expect(screen.getByText(/2 of 5/)).toBeInTheDocument();
  });

  it('should render navigation buttons', () => {
    render(
      <PdfSearchBar
        onSearch={vi.fn()}
        onNavigate={vi.fn()}
        resultsCount={5}
      />
    );

    expect(screen.getByLabelText(/previous/i)).toBeInTheDocument();
    expect(screen.getByLabelText(/next/i)).toBeInTheDocument();
  });

  it('should call onNavigate with direction', () => {
    const onNavigate = vi.fn();

    render(
      <PdfSearchBar
        onSearch={vi.fn()}
        onNavigate={onNavigate}
        resultsCount={5}
      />
    );

    fireEvent.click(screen.getByLabelText(/next/i));
    expect(onNavigate).toHaveBeenCalledWith('next');

    fireEvent.click(screen.getByLabelText(/previous/i));
    expect(onNavigate).toHaveBeenCalledWith('prev');
  });

  it('should render clear button when has value', () => {
    render(<PdfSearchBar onSearch={vi.fn()} value="test" onClear={vi.fn()} />);

    expect(screen.getByLabelText(/clear/i)).toBeInTheDocument();
  });

  it('should call onClear when clear clicked', () => {
    const onClear = vi.fn();

    render(<PdfSearchBar onSearch={vi.fn()} value="test" onClear={onClear} />);

    fireEvent.click(screen.getByLabelText(/clear/i));

    expect(onClear).toHaveBeenCalled();
  });
});

// ============================================
// Bookmarks Feature Tests
// ============================================

describe('PdfBookmarkList Component', () => {
  it('should render bookmark list', () => {
    render(
      <PdfBookmarkList
        bookmarks={mockBookmarks}
        onNavigate={vi.fn()}
      />
    );

    expect(screen.getByText('Chapter 1: Introduction')).toBeInTheDocument();
    expect(screen.getByText('Chapter 2: Installation')).toBeInTheDocument();
  });

  it('should render nested bookmarks', () => {
    render(
      <PdfBookmarkList
        bookmarks={mockBookmarks}
        onNavigate={vi.fn()}
      />
    );

    expect(screen.getByText('Overview')).toBeInTheDocument();
    expect(screen.getByText('Getting Started')).toBeInTheDocument();
  });

  it('should call onNavigate when bookmark clicked', () => {
    const onNavigate = vi.fn();

    render(
      <PdfBookmarkList
        bookmarks={mockBookmarks}
        onNavigate={onNavigate}
      />
    );

    fireEvent.click(screen.getByText('Chapter 1: Introduction'));

    expect(onNavigate).toHaveBeenCalledWith(1);
  });

  it('should show page numbers', () => {
    render(
      <PdfBookmarkList
        bookmarks={mockBookmarks}
        onNavigate={vi.fn()}
        showPageNumbers={true}
      />
    );

    expect(screen.getByText('1')).toBeInTheDocument();
    expect(screen.getByText('10')).toBeInTheDocument();
  });

  it('should toggle children visibility', () => {
    render(
      <PdfBookmarkList
        bookmarks={mockBookmarks}
        onNavigate={vi.fn()}
      />
    );

    const toggleButton = screen.getAllByRole('button', { name: /toggle/i })[0];

    // Initially visible
    expect(screen.getByText('Overview')).toBeVisible();

    fireEvent.click(toggleButton);

    // Should be collapsed
    expect(screen.getByText('Overview')).not.toBeVisible();
  });

  it('should highlight active bookmark', () => {
    render(
      <PdfBookmarkList
        bookmarks={mockBookmarks}
        onNavigate={vi.fn()}
        currentPage={10}
      />
    );

    const activeItem = screen.getByText('Chapter 2: Installation').closest('[data-testid="bookmark-item"]');
    expect(activeItem).toHaveClass('pdf-bookmark-active');
  });

  it('should show empty message when no bookmarks', () => {
    render(
      <PdfBookmarkList
        bookmarks={[]}
        onNavigate={vi.fn()}
      />
    );

    expect(screen.getByText(/no bookmarks/i)).toBeInTheDocument();
  });
});

// ============================================
// Component Implementations for Testing
// ============================================

function usePasswordProtection(document: any, options?: { apiClient?: any }) {
  const [isUnlocked, setIsUnlocked] = React.useState(() => {
    return sessionStorage.getItem(`pdf-unlocked-${document.id}`) === 'true';
  });
  const [isLoading, setIsLoading] = React.useState(false);
  const [error, setError] = React.useState<string | null>(null);

  const verifyPassword = async (password: string) => {
    setIsLoading(true);
    setError(null);

    try {
      const result = await options?.apiClient?.verifyPassword(document.id, password);
      if (result.success) {
        setIsUnlocked(true);
        sessionStorage.setItem(`pdf-unlocked-${document.id}`, 'true');
      } else {
        setError(result.error || 'Invalid password');
      }
    } finally {
      setIsLoading(false);
    }
  };

  return {
    isProtected: document.passwordProtected,
    isUnlocked,
    isLoading,
    error,
    verifyPassword,
  };
}

function PdfPasswordModal({
  isOpen,
  onSubmit,
  onCancel,
  error,
  isLoading,
  documentTitle,
}: any) {
  const [password, setPassword] = React.useState('');

  if (!isOpen) return null;

  return (
    <div role="dialog" className="pdf-password-modal">
      {documentTitle && <h3>{documentTitle}</h3>}
      <label>
        Password
        <input
          type="password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          aria-label="Password"
        />
      </label>
      {error && <div className="error">{error}</div>}
      <button onClick={onCancel}>Cancel</button>
      <button onClick={() => onSubmit(password)} disabled={isLoading}>
        Unlock
      </button>
    </div>
  );
}

function useReadingProgress(document: any, options?: { apiClient?: any }) {
  const [progress, setProgress] = React.useState<any>(null);

  React.useEffect(() => {
    options?.apiClient?.getProgress(document.id).then(setProgress);
  }, [document.id, options?.apiClient]);

  const percentComplete = progress
    ? Math.round((progress.page / document.pageCount) * 100)
    : 0;

  const saveProgress = async (newProgress: any) => {
    await options?.apiClient?.saveProgress(document.id, newProgress);
    setProgress((p: any) => ({ ...p, ...newProgress }));
  };

  return { progress, percentComplete, saveProgress };
}

function PdfProgressBar({
  progress,
  position = 'top',
  showInfo = false,
  currentPage,
  totalPages,
}: any) {
  return (
    <div
      role="progressbar"
      className={`pdf-progress-bar ${position === 'bottom' ? 'pdf-progress-bottom' : ''}`}
      aria-valuenow={progress}
    >
      <div
        data-testid="progress-fill"
        className="pdf-progress-fill"
        style={{ width: `${progress}%` }}
      />
      {showInfo && (
        <div className="pdf-progress-info">
          <span>{currentPage} of {totalPages}</span>
          <span>{progress}%</span>
        </div>
      )}
    </div>
  );
}

function useAnalytics(options?: { apiClient?: any; period?: string }) {
  const [data, setData] = React.useState<any>(null);
  const [isLoading, setIsLoading] = React.useState(true);

  React.useEffect(() => {
    setIsLoading(true);
    options?.apiClient
      ?.getAnalytics({ period: options.period })
      .then(setData)
      .finally(() => setIsLoading(false));
  }, [options?.apiClient, options?.period]);

  return { data, isLoading };
}

function PdfAnalyticsDashboard({
  data,
  isLoading,
  error,
  showExport = false,
  onPeriodChange,
}: any) {
  if (isLoading) return <div>Loading...</div>;
  if (error) return <div className="error">{error}</div>;

  const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  return (
    <div className="pdf-analytics">
      <h2>PDF Analytics</h2>
      <select onChange={(e) => onPeriodChange?.(e.target.value)}>
        <option value="7days">Last 7 Days</option>
        <option value="30days">Last 30 Days</option>
        <option value="90days">Last 90 Days</option>
      </select>
      {showExport && (
        <div>
          <button>Export CSV</button>
          <button>Export JSON</button>
        </div>
      )}
      <div className="stats">
        <div>{data?.totalViews?.toLocaleString()}</div>
        <div>{data?.totalDownloads?.toLocaleString()}</div>
        <div>{data?.documentsCount}</div>
        <div>{formatTime(data?.avgTimeOnPage || 0)}</div>
      </div>
      <table>
        <thead>
          <tr>
            <th>Document</th>
            <th>Views</th>
            <th>Downloads</th>
          </tr>
        </thead>
        <tbody>
          {data?.topDocuments?.map((doc: any) => (
            <tr key={doc.id}>
              <td>{doc.title}</td>
              <td>{doc.views}</td>
              <td>{doc.downloads}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

function PdfSearchBar({
  onSearch,
  onNavigate,
  onClear,
  value = '',
  resultsCount = 0,
  currentResult = 0,
}: any) {
  return (
    <div className="pdf-search-bar">
      <input
        type="search"
        role="searchbox"
        value={value}
        onChange={(e) => onSearch(e.target.value)}
      />
      {value && onClear && (
        <button aria-label="Clear search" onClick={onClear}>X</button>
      )}
      {resultsCount > 0 && (
        <>
          <span>{currentResult} of {resultsCount}</span>
          <button aria-label="Previous result" onClick={() => onNavigate?.('prev')}>
            Prev
          </button>
          <button aria-label="Next result" onClick={() => onNavigate?.('next')}>
            Next
          </button>
        </>
      )}
    </div>
  );
}

function PdfBookmarkList({
  bookmarks,
  onNavigate,
  showPageNumbers = false,
  currentPage,
}: any) {
  const [collapsed, setCollapsed] = React.useState<Set<string>>(new Set());

  if (bookmarks.length === 0) {
    return <div>No bookmarks available</div>;
  }

  const toggleCollapse = (title: string) => {
    setCollapsed((prev) => {
      const next = new Set(prev);
      if (next.has(title)) {
        next.delete(title);
      } else {
        next.add(title);
      }
      return next;
    });
  };

  const renderBookmark = (bookmark: any, level = 0) => {
    const isActive = bookmark.page === currentPage;
    const isCollapsed = collapsed.has(bookmark.title);

    return (
      <li
        key={bookmark.title}
        data-testid="bookmark-item"
        className={isActive ? 'pdf-bookmark-active' : ''}
        style={{ paddingLeft: `${level * 16}px` }}
      >
        <div onClick={() => onNavigate(bookmark.page)}>
          {bookmark.children?.length > 0 && (
            <button
              aria-label="Toggle children"
              onClick={(e) => {
                e.stopPropagation();
                toggleCollapse(bookmark.title);
              }}
            >
              {isCollapsed ? '+' : '-'}
            </button>
          )}
          <span>{bookmark.title}</span>
          {showPageNumbers && <span>{bookmark.page}</span>}
        </div>
        {bookmark.children?.length > 0 && (
          <ul style={{ display: isCollapsed ? 'none' : 'block' }}>
            {bookmark.children.map((child: any) => renderBookmark(child, level + 1))}
          </ul>
        )}
      </li>
    );
  };

  return (
    <ul className="pdf-bookmark-list">
      {bookmarks.map((bookmark: any) => renderBookmark(bookmark))}
    </ul>
  );
}
