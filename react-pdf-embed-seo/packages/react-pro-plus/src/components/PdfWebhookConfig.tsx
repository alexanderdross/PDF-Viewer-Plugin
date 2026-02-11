/**
 * PdfWebhookConfig Component
 * Webhook configuration management interface
 */

import React, { useState } from 'react';
import { useWebhooks } from '../hooks/useWebhooks';
import type { WebhookConfig, WebhookEvent, WebhookConfigProps } from '../types';

const WEBHOOK_EVENTS: { value: WebhookEvent; label: string }[] = [
  { value: 'document.viewed', label: 'Document Viewed' },
  { value: 'document.downloaded', label: 'Document Downloaded' },
  { value: 'document.printed', label: 'Document Printed' },
  { value: 'password.success', label: 'Password Success' },
  { value: 'password.failed', label: 'Password Failed' },
  { value: 'annotation.created', label: 'Annotation Created' },
  { value: 'annotation.deleted', label: 'Annotation Deleted' },
  { value: 'version.created', label: 'Version Created' },
  { value: 'progress.updated', label: 'Progress Updated' },
];

interface WebhookFormState {
  name: string;
  url: string;
  secret: string;
  events: WebhookEvent[];
  active: boolean;
}

const initialFormState: WebhookFormState = {
  name: '',
  url: '',
  secret: '',
  events: [],
  active: true,
};

export const PdfWebhookConfig: React.FC<WebhookConfigProps> = ({
  webhooks: initialWebhooks,
  onWebhookCreate,
  onWebhookUpdate,
  onWebhookDelete,
  onWebhookTest,
}) => {
  const { webhooks, createWebhook, updateWebhook, deleteWebhook, testWebhook, loading } = useWebhooks();
  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [formState, setFormState] = useState<WebhookFormState>(initialFormState);
  const [testResults, setTestResults] = useState<Record<number, { success: boolean; message: string }>>({});

  const displayWebhooks = webhooks.length > 0 ? webhooks : initialWebhooks;

  const handleEdit = (webhook: WebhookConfig) => {
    setEditingId(webhook.id || null);
    setFormState({
      name: webhook.name,
      url: webhook.url,
      secret: webhook.secret || '',
      events: webhook.events,
      active: webhook.active,
    });
    setShowForm(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    const webhookData: WebhookConfig = {
      ...formState,
      id: editingId || undefined,
    };

    if (editingId) {
      const result = await updateWebhook(editingId, webhookData);
      if (result) {
        onWebhookUpdate?.(result);
      }
    } else {
      const result = await createWebhook(webhookData);
      if (result) {
        onWebhookCreate?.(result);
      }
    }

    setShowForm(false);
    setEditingId(null);
    setFormState(initialFormState);
  };

  const handleDelete = async (id: number) => {
    if (!confirm('Are you sure you want to delete this webhook?')) return;

    const success = await deleteWebhook(id);
    if (success) {
      onWebhookDelete?.(id);
    }
  };

  const handleTest = async (id: number) => {
    const result = await testWebhook(id);
    setTestResults(prev => ({
      ...prev,
      [id]: result,
    }));
    onWebhookTest?.(id);
  };

  const toggleEvent = (event: WebhookEvent) => {
    setFormState(prev => ({
      ...prev,
      events: prev.events.includes(event)
        ? prev.events.filter(e => e !== event)
        : [...prev.events, event],
    }));
  };

  const generateSecret = () => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let secret = '';
    for (let i = 0; i < 32; i++) {
      secret += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    setFormState(prev => ({ ...prev, secret }));
  };

  return (
    <div className="pdf-webhook-config">
      <div className="pdf-webhook-header">
        <h3>Webhook Configuration</h3>
        <button
          className="pdf-webhook-add-btn"
          onClick={() => {
            setShowForm(true);
            setEditingId(null);
            setFormState(initialFormState);
          }}
        >
          + Add Webhook
        </button>
      </div>

      {showForm && (
        <form className="pdf-webhook-form" onSubmit={handleSubmit}>
          <h4>{editingId ? 'Edit Webhook' : 'New Webhook'}</h4>

          <div className="pdf-webhook-form-group">
            <label>Name</label>
            <input
              type="text"
              value={formState.name}
              onChange={e => setFormState(prev => ({ ...prev, name: e.target.value }))}
              placeholder="My Webhook"
              required
            />
          </div>

          <div className="pdf-webhook-form-group">
            <label>URL</label>
            <input
              type="url"
              value={formState.url}
              onChange={e => setFormState(prev => ({ ...prev, url: e.target.value }))}
              placeholder="https://example.com/webhook"
              required
            />
          </div>

          <div className="pdf-webhook-form-group">
            <label>Secret</label>
            <div className="pdf-webhook-secret-input">
              <input
                type="text"
                value={formState.secret}
                onChange={e => setFormState(prev => ({ ...prev, secret: e.target.value }))}
                placeholder="Signing secret"
              />
              <button type="button" onClick={generateSecret}>Generate</button>
            </div>
            <small>Used to verify webhook signatures</small>
          </div>

          <div className="pdf-webhook-form-group">
            <label>Events</label>
            <div className="pdf-webhook-events">
              {WEBHOOK_EVENTS.map(({ value, label }) => (
                <label key={value} className="pdf-webhook-event">
                  <input
                    type="checkbox"
                    checked={formState.events.includes(value)}
                    onChange={() => toggleEvent(value)}
                  />
                  {label}
                </label>
              ))}
            </div>
          </div>

          <div className="pdf-webhook-form-group">
            <label>
              <input
                type="checkbox"
                checked={formState.active}
                onChange={e => setFormState(prev => ({ ...prev, active: e.target.checked }))}
              />
              Active
            </label>
          </div>

          <div className="pdf-webhook-form-actions">
            <button type="button" onClick={() => setShowForm(false)}>Cancel</button>
            <button type="submit" disabled={loading}>
              {loading ? 'Saving...' : editingId ? 'Update' : 'Create'}
            </button>
          </div>
        </form>
      )}

      <div className="pdf-webhook-list">
        {displayWebhooks.length === 0 ? (
          <div className="pdf-webhook-empty">
            No webhooks configured
          </div>
        ) : (
          displayWebhooks.map(webhook => (
            <div key={webhook.id} className={`pdf-webhook-item ${!webhook.active ? 'inactive' : ''}`}>
              <div className="pdf-webhook-item-header">
                <span className="pdf-webhook-name">{webhook.name}</span>
                <span className={`pdf-webhook-status ${webhook.active ? 'active' : 'inactive'}`}>
                  {webhook.active ? 'Active' : 'Inactive'}
                </span>
              </div>

              <div className="pdf-webhook-url">{webhook.url}</div>

              <div className="pdf-webhook-events-list">
                {webhook.events.map(event => (
                  <span key={event} className="pdf-webhook-event-tag">{event}</span>
                ))}
              </div>

              {testResults[webhook.id!] && (
                <div className={`pdf-webhook-test-result ${testResults[webhook.id!].success ? 'success' : 'error'}`}>
                  {testResults[webhook.id!].message}
                </div>
              )}

              <div className="pdf-webhook-item-actions">
                <button onClick={() => handleTest(webhook.id!)}>Test</button>
                <button onClick={() => handleEdit(webhook)}>Edit</button>
                <button onClick={() => handleDelete(webhook.id!)} className="delete">Delete</button>
              </div>
            </div>
          ))
        )}
      </div>

      <style>{`
        .pdf-webhook-config {
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
          background: #fff;
          border-radius: 8px;
          padding: 20px;
          box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .pdf-webhook-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
        }
        .pdf-webhook-header h3 {
          margin: 0;
          font-size: 18px;
        }
        .pdf-webhook-add-btn {
          padding: 8px 16px;
          background: #4caf50;
          color: #fff;
          border: none;
          border-radius: 4px;
          cursor: pointer;
        }
        .pdf-webhook-add-btn:hover {
          background: #43a047;
        }
        .pdf-webhook-form {
          background: #f9f9f9;
          padding: 20px;
          border-radius: 8px;
          margin-bottom: 20px;
        }
        .pdf-webhook-form h4 {
          margin: 0 0 16px;
        }
        .pdf-webhook-form-group {
          margin-bottom: 16px;
        }
        .pdf-webhook-form-group label {
          display: block;
          margin-bottom: 4px;
          font-weight: 500;
          font-size: 14px;
        }
        .pdf-webhook-form-group input[type="text"],
        .pdf-webhook-form-group input[type="url"] {
          width: 100%;
          padding: 8px 12px;
          border: 1px solid #ddd;
          border-radius: 4px;
          font-size: 14px;
        }
        .pdf-webhook-form-group small {
          display: block;
          margin-top: 4px;
          color: #888;
          font-size: 12px;
        }
        .pdf-webhook-secret-input {
          display: flex;
          gap: 8px;
        }
        .pdf-webhook-secret-input input {
          flex: 1;
        }
        .pdf-webhook-secret-input button {
          padding: 8px 12px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background: #fff;
          cursor: pointer;
        }
        .pdf-webhook-events {
          display: flex;
          flex-wrap: wrap;
          gap: 8px;
        }
        .pdf-webhook-event {
          display: flex;
          align-items: center;
          gap: 4px;
          font-size: 13px;
          cursor: pointer;
        }
        .pdf-webhook-form-actions {
          display: flex;
          gap: 8px;
          justify-content: flex-end;
        }
        .pdf-webhook-form-actions button {
          padding: 8px 16px;
          border: 1px solid #ddd;
          border-radius: 4px;
          cursor: pointer;
        }
        .pdf-webhook-form-actions button[type="submit"] {
          background: #2196f3;
          color: #fff;
          border-color: #2196f3;
        }
        .pdf-webhook-list {
          display: flex;
          flex-direction: column;
          gap: 12px;
        }
        .pdf-webhook-empty {
          padding: 40px;
          text-align: center;
          color: #888;
        }
        .pdf-webhook-item {
          padding: 16px;
          border: 1px solid #eee;
          border-radius: 8px;
        }
        .pdf-webhook-item.inactive {
          opacity: 0.6;
        }
        .pdf-webhook-item-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 8px;
        }
        .pdf-webhook-name {
          font-weight: 600;
        }
        .pdf-webhook-status {
          padding: 2px 8px;
          border-radius: 4px;
          font-size: 11px;
        }
        .pdf-webhook-status.active {
          background: #e8f5e9;
          color: #4caf50;
        }
        .pdf-webhook-status.inactive {
          background: #fafafa;
          color: #888;
        }
        .pdf-webhook-url {
          font-family: monospace;
          font-size: 13px;
          color: #666;
          margin-bottom: 8px;
          word-break: break-all;
        }
        .pdf-webhook-events-list {
          display: flex;
          flex-wrap: wrap;
          gap: 4px;
          margin-bottom: 12px;
        }
        .pdf-webhook-event-tag {
          padding: 2px 6px;
          background: #e3f2fd;
          color: #1976d2;
          border-radius: 4px;
          font-size: 11px;
        }
        .pdf-webhook-test-result {
          padding: 8px;
          border-radius: 4px;
          margin-bottom: 8px;
          font-size: 13px;
        }
        .pdf-webhook-test-result.success {
          background: #e8f5e9;
          color: #2e7d32;
        }
        .pdf-webhook-test-result.error {
          background: #ffebee;
          color: #c62828;
        }
        .pdf-webhook-item-actions {
          display: flex;
          gap: 8px;
        }
        .pdf-webhook-item-actions button {
          padding: 4px 12px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background: #fff;
          font-size: 12px;
          cursor: pointer;
        }
        .pdf-webhook-item-actions button:hover {
          background: #f5f5f5;
        }
        .pdf-webhook-item-actions button.delete {
          color: #f44336;
          border-color: #f44336;
        }
        .pdf-webhook-item-actions button.delete:hover {
          background: #ffebee;
        }
      `}</style>
    </div>
  );
};

export default PdfWebhookConfig;
