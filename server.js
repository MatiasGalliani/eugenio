// Load environment variables from .env file
require('dotenv').config();

const express = require('express');
const path = require('path');
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || '0.0.0.0';
// Email disabled: removing all mail functionality

// Bypass CORS completely: set permissive headers for all requests and short-circuit preflight
app.use((req, res, next) => {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH');
    res.setHeader('Access-Control-Allow-Headers', '*');
    res.setHeader('Access-Control-Max-Age', '86400');
    if (req.method === 'OPTIONS') {
        return res.sendStatus(204);
    }
    next();
});

// Middleware for JSON parsing
app.use(express.json());

// API Routes - Define BEFORE static files to avoid conflicts
app.get('/api/health', (req, res) => {
    res.json({ status: 'ok', message: 'Server is running!' });
});

// API endpoint to receive lead data
app.post('/api/leads', async (req, res) => {
    console.log('📥 POST /api/leads - Request received');
    const leadData = req.body;
    
    // Log the lead data
    console.log('📧 New Lead Received:', JSON.stringify(leadData, null, 2));
    
    // Email disabled; no mail sending

    // Forward to Make.com webhook if configured (non-blocking)
    const makeWebhookUrl = process.env.MAKE_WEBHOOK_URL || '';
    if (makeWebhookUrl) {
        try {
            const controller = new AbortController();
            const timeout = setTimeout(() => controller.abort(), 10000);
            fetch(makeWebhookUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(leadData),
                signal: controller.signal
            })
            .then(async (r) => {
                clearTimeout(timeout);
                if (!r.ok) {
                    const text = await r.text().catch(() => '');
                    console.warn(`⚠️  Make webhook responded ${r.status}: ${text}`);
                } else {
                    console.log('✅ Lead forwarded to Make webhook');
                }
            })
            .catch((err) => {
                clearTimeout(timeout);
                console.warn(`⚠️  Make webhook error: ${err.message}`);
            });
        } catch (err) {
            console.warn(`⚠️  Make webhook setup error: ${err.message}`);
        }
    } else {
        console.log('ℹ️  MAKE_WEBHOOK_URL not set; skipping webhook forward.');
    }
    
    // Email removed: no email sending
    
    // TODO: Add your business logic here:
    // - Save to database
    // - Integrate with CRM
    // - etc.
    
    // Return success response
    res.json({ 
        status: 'success', 
        message: 'Lead received successfully',
        timestamp: new Date().toISOString()
    });
});

// Static files - Serve AFTER API routes
app.use(express.static(path.join(__dirname, '.')));

// Prevent favicon.ico 404 noise
app.get('/favicon.ico', (req, res) => {
    res.status(204).end();
});

// Email removed: no SMTP configuration

// Email removed: no branch map needed

// Email removed: no transporter

// Email removed: no transporter

// Email removed: no alternative SMTP configs

// Email removed: no verification

// Helper function to format lead information for email (plain text version)
// Email removed: no email text formatter

// Helper function to format lead information as beautiful HTML email
// Email removed: no HTML formatter

// Root route
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// Start server
const server = app.listen(PORT, HOST, () => {
    const hostForLog = HOST === '0.0.0.0' ? '0.0.0.0' : HOST;
    console.log(`🚀 Server running on http://${hostForLog}:${PORT}`);
    console.log('✉️  Email disabled; forwarding leads only if MAKE_WEBHOOK_URL is set.');
});

// Graceful shutdown to avoid noisy SIGTERM errors
function shutdown(signal) {
    console.log(`\n${signal} received, shutting down gracefully...`);
    server.close(() => {
        console.log('HTTP server closed.');
        process.exit(0);
    });
    // Force exit if not closed in time
    setTimeout(() => {
        console.warn('Forcing shutdown after 10s...');
        process.exit(1);
    }, 10000).unref();
}

process.on('SIGTERM', () => shutdown('SIGTERM'));
process.on('SIGINT', () => shutdown('SIGINT'));

