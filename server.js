// Load environment variables from .env file
require('dotenv').config();

const express = require('express');
const path = require('path');
const nodemailer = require('nodemailer');

const app = express();
const PORT = process.env.PORT || 3000;

// Enable CORS FIRST - before other middleware
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
    
    // Handle preflight requests
    if (req.method === 'OPTIONS') {
        return res.sendStatus(200);
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
    
    // Send email notification to agents
    // Configure the recipient email address (where agents will receive leads)
    const agentEmail = process.env.AGENT_EMAIL || 'agent@example.com'; // Change this to your agent email
    
    if (emailConfig.auth.user && emailConfig.auth.pass && agentEmail !== 'agent@example.com') {
        try {
            const mailOptions = {
                from: `"Assistente Virtuale €ugenio" <${emailConfig.auth.user}>`,
                to: agentEmail, // Can be a comma-separated list for multiple agents
                subject: `🔔 Nuovo Lead - ${leadData.data.nome || 'Cliente'} ${leadData.data.cognome || ''} - ${branchMap[leadData.branch] || 'Richiesta'}`,
                text: formatLeadEmail(leadData),
                html: formatLeadEmailHTML(leadData)
            };
            
            const info = await transporter.sendMail(mailOptions);
            console.log('✅ Email sent successfully:', info.messageId);
        } catch (error) {
            console.error('❌ Error sending email:', error.message);
            // Don't fail the request if email fails, just log it
        }
    } else {
        console.log('⚠️  Email not sent - Please configure SMTP_USER, SMTP_PASS, and AGENT_EMAIL environment variables');
    }
    
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

// Email configuration
// You can set these via environment variables or replace with your SMTP settings
const emailConfig = {
    host: process.env.SMTP_HOST || 'smtp.gmail.com',
    port: Number(process.env.SMTP_PORT || 587),
    secure: String(process.env.SMTP_SECURE || '').toLowerCase() === 'true',
    auth: {
        user: process.env.SMTP_USER || '',
        pass: process.env.SMTP_PASS || ''
    },
    requireTLS: String(process.env.SMTP_REQUIRE_TLS || '').toLowerCase() === 'true',
    tls: {
        rejectUnauthorized: String(process.env.SMTP_REJECT_UNAUTHORIZED || 'true').toLowerCase() === 'true'
    }
};

// Branch mapping for email formatting
const branchMap = {
    'mutui_prestiti': 'Mutui e Prestiti',
    'cessioni_quinto': 'Cessione del Quinto',
    'leasing_auto': 'Leasing e Finanziamenti Auto',
    'ristrutturazione': 'Ristrutturazione e Liquidità',
    'assicurazioni': 'Assicurazioni',
    'altro': 'Altro / Consulenza Generale'
};

// Configure email transporter
const transporter = nodemailer.createTransport(emailConfig);

// Verify email configuration (only in development)
if (emailConfig.auth.user && emailConfig.auth.pass) {
    transporter.verify(function(error, success) {
        if (error) {
            console.log('⚠️  Email configuration error:', error.message);
            console.log('⚠️  Emails will not be sent. Please configure SMTP settings.');
        } else {
            console.log('✅ Email server is ready to send messages');
        }
    });
}

// Helper function to format lead information for email (plain text version)
function formatLeadEmail(leadData) {
    let emailBody = 'Ciao,\n\nÈ arrivato un nuovo lead!\n\n';
    emailBody += '='.repeat(50) + '\n\n';
    
    // Personal information
    emailBody += '📋 INFORMAZIONI PERSONALI:\n';
    emailBody += `Nome: ${leadData.data.nome || 'Non fornito'}\n`;
    emailBody += `Cognome: ${leadData.data.cognome || 'Non fornito'}\n`;
    emailBody += `Email: ${leadData.data.email || 'Non fornita'}\n`;
    emailBody += `Telefono: ${leadData.data.telefono || 'Non fornito'}\n\n`;
    
    // Service type
    emailBody += '🎯 TIPO DI SERVIZIO:\n';
    emailBody += `${branchMap[leadData.branch] || 'Non specificato'}\n\n`;
    
    // Specific service details
    if (leadData.data.tipo) {
        emailBody += `📌 Dettaglio: ${leadData.data.tipo}\n\n`;
    }
    
    // Additional information based on branch
    if (leadData.branch === 'mutui_prestiti') {
        if (leadData.data.cliente) {
            emailBody += `👤 Tipo cliente: ${leadData.data.cliente}\n`;
        }
        if (leadData.data.finanziamento_corso) {
            emailBody += `💰 Finanziamento in corso: ${leadData.data.finanziamento_corso}\n`;
        }
    } else if (leadData.branch === 'cessioni_quinto') {
        if (leadData.data.cessione_attiva) {
            emailBody += `📄 Cessione attiva: ${leadData.data.cessione_attiva}\n`;
        }
    } else if (leadData.branch === 'leasing_auto') {
        if (leadData.data.veicolo) {
            emailBody += `🚗 Tipo veicolo: ${leadData.data.veicolo}\n`;
        }
    } else if (leadData.branch === 'ristrutturazione') {
        if (leadData.data.finanziamento_corso) {
            emailBody += `💰 Finanziamento in corso: ${leadData.data.finanziamento_corso}\n`;
        }
    }
    
    // Optional message
    if (leadData.data.message) {
        emailBody += `\n💬 MESSAGGIO AGGIUNTIVO:\n${leadData.data.message}\n\n`;
    }
    
    emailBody += '\n' + '='.repeat(50) + '\n';
    emailBody += `\nData richiesta: ${new Date().toLocaleString('it-IT')}\n`;
    
    return emailBody;
}

// Helper function to format lead information as beautiful HTML email
function formatLeadEmailHTML(leadData) {
    const logoUrl = 'https://creditplan.it/wp-content/uploads/2023/02/LOGO-CREDITPLAN.png';
    
    // Get additional info based on branch
    let additionalInfo = [];
    if (leadData.branch === 'mutui_prestiti') {
        if (leadData.data.cliente) {
            additionalInfo.push({ label: 'Tipo cliente', value: leadData.data.cliente });
        }
        if (leadData.data.finanziamento_corso) {
            additionalInfo.push({ label: 'Finanziamento in corso', value: leadData.data.finanziamento_corso });
        }
    } else if (leadData.branch === 'cessioni_quinto') {
        if (leadData.data.cessione_attiva) {
            additionalInfo.push({ label: 'Cessione attiva', value: leadData.data.cessione_attiva });
        }
    } else if (leadData.branch === 'leasing_auto') {
        if (leadData.data.veicolo) {
            additionalInfo.push({ label: 'Tipo veicolo', value: leadData.data.veicolo });
        }
    } else if (leadData.branch === 'ristrutturazione') {
        if (leadData.data.finanziamento_corso) {
            additionalInfo.push({ label: 'Finanziamento in corso', value: leadData.data.finanziamento_corso });
        }
    }
    
    const dateTime = new Date().toLocaleString('it-IT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    return `
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuovo Lead - Creditplan</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f5f7fb; line-height: 1.6;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f7fb; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" style="max-width: 600px; width: 100%; border-collapse: collapse; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header with Logo -->
                    <tr>
                        <td style="background-color: #f1f5f9; padding: 40px 30px; text-align: center;">
                            <img src="${logoUrl}" alt="Creditplan Logo" style="max-width: 200px; height: auto; display: block; margin: 0 auto;" />
                        </td>
                    </tr>
                    
                    <!-- Alert Banner -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px; background-color: #ffffff;">
                            <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff; padding: 16px 20px; border-radius: 8px; text-align: center; font-size: 18px; font-weight: 600; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);">
                                🔔 Nuovo Lead Ricevuto
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px; background-color: #ffffff;">
                            
                            <!-- Personal Information Section -->
                            <div style="background-color: #f8fafc; border-left: 4px solid #3b82f6; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
                                <h2 style="margin: 0 0 16px 0; color: #1e293b; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 20px;">📋</span> Informazioni Personali
                                </h2>
                                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 8px 0; color: #64748b; font-size: 14px; width: 120px; font-weight: 500;">Nome:</td>
                                        <td style="padding: 8px 0; color: #0f172a; font-size: 15px; font-weight: 600;">${leadData.data.nome || 'Non fornito'}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">Cognome:</td>
                                        <td style="padding: 8px 0; color: #0f172a; font-size: 15px; font-weight: 600;">${leadData.data.cognome || 'Non fornito'}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">Email:</td>
                                        <td style="padding: 8px 0;">
                                            <a href="mailto:${leadData.data.email || ''}" style="color: #3b82f6; text-decoration: none; font-size: 15px; font-weight: 600;">${leadData.data.email || 'Non fornita'}</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500;">Telefono:</td>
                                        <td style="padding: 8px 0;">
                                            <a href="tel:${leadData.data.telefono || ''}" style="color: #3b82f6; text-decoration: none; font-size: 15px; font-weight: 600;">${leadData.data.telefono || 'Non fornito'}</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Service Type Section -->
                            <div style="background-color: #f8fafc; border-left: 4px solid #10b981; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
                                <h2 style="margin: 0 0 12px 0; color: #1e293b; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 20px;">🎯</span> Tipo di Servizio
                                </h2>
                                <p style="margin: 0; color: #0f172a; font-size: 16px; font-weight: 600; padding: 12px; background-color: #ffffff; border-radius: 6px; display: inline-block;">
                                    ${branchMap[leadData.branch] || 'Non specificato'}
                                </p>
                                ${leadData.data.tipo ? `
                                <p style="margin: 12px 0 0 0; color: #64748b; font-size: 14px;">
                                    <strong>Dettaglio:</strong> <span style="color: #0f172a;">${leadData.data.tipo}</span>
                                </p>
                                ` : ''}
                            </div>
                            
                            ${additionalInfo.length > 0 ? `
                            <!-- Additional Information Section -->
                            <div style="background-color: #f8fafc; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
                                <h2 style="margin: 0 0 16px 0; color: #1e293b; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 20px;">ℹ️</span> Informazioni Aggiuntive
                                </h2>
                                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                    ${additionalInfo.map(info => `
                                    <tr>
                                        <td style="padding: 8px 0; color: #64748b; font-size: 14px; font-weight: 500; width: 180px;">${info.label}:</td>
                                        <td style="padding: 8px 0; color: #0f172a; font-size: 15px; font-weight: 600;">${info.value}</td>
                                    </tr>
                                    `).join('')}
                                </table>
                            </div>
                            ` : ''}
                            
                            ${leadData.data.message ? `
                            <!-- Message Section -->
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 8px; margin-bottom: 24px;">
                                <h2 style="margin: 0 0 12px 0; color: #1e293b; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 20px;">💬</span> Messaggio Aggiuntivo
                                </h2>
                                <div style="background-color: #ffffff; padding: 16px; border-radius: 6px; color: #1e293b; font-size: 14px; line-height: 1.6; white-space: pre-wrap; border: 1px solid #fde68a;">
                                    ${leadData.data.message.replace(/\n/g, '<br>')}
                                </div>
                            </div>
                            ` : ''}
                            
                            <!-- Timestamp -->
                            <div style="text-align: center; padding: 20px 0; border-top: 1px solid #e5e7eb; margin-top: 30px;">
                                <p style="margin: 0; color: #64748b; font-size: 12px;">
                                    Richiesta ricevuta il ${dateTime}
                                </p>
                            </div>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 8px 0; color: #64748b; font-size: 12px;">
                                Questo messaggio è stato generato automaticamente dall'assistente virtuale <strong>€ugenio</strong>
                            </p>
                            <p style="margin: 0; color: #94a3b8; font-size: 11px;">
                                Creditplan - Gestione Lead Automatica
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
    `.trim();
}

// Root route
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// Start server
app.listen(PORT, () => {
    console.log(`🚀 Server running on http://localhost:${PORT}`);
    console.log(`📧 Email config: ${emailConfig.auth.user ? 'Configured' : 'Not configured - set SMTP_USER and SMTP_PASS'}`);
});

