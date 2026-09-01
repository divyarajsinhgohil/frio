<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'config.php';
$settings   = api_fetch('settings.php') ?? [];
$page_title  = 'Contact Us | FRIO Industrial';
$meta_desc   = 'Get in touch with FRIO Industrial. Send an enquiry for product specifications, pricing, or technical support.';
$active_page = 'contact';

$preset_product = isset($_GET['product']) ? trim($_GET['product']) : '';
$prefilled_msg = '';
$prefilled_subject = '';
if (!empty($preset_product)) {
    $prefilled_msg = "Hello, I am interested in inquiring about your product: " . htmlspecialchars($preset_product) . ".\n\nPlease provide technical specifications, bulk availability, and price quotes.";
    $prefilled_subject = "Product Enquiry";
}
include 'includes/header.php';
?>
</div> <!-- Close global wrapper for full-bleed hero banner -->

<?php
$contact_banner_src = '';
$preset = 'assets/imag/banners/contect.png';
if (file_exists(__DIR__ . '/' . $preset)) {
    $contact_banner_src = $preset;
} else {
    $contact_banner_src = asset_url($preset);
}
?>

<!-- Hero Banner -->
<section class="relative overflow-hidden py-16 md:py-24 bg-gradient-to-b from-[#071322] to-[#0b192c] flex items-center justify-center border-b border-white/5">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(254,214,91,0.05),transparent_40%)] pointer-events-none"></div>
    <div class="text-center px-8 relative z-10">
        <nav class="flex items-center gap-2 text-sm text-white/80 mb-5 justify-center">
            <a href="index.php" class="hover:text-white transition-colors font-medium">Home</a>
            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            <span class="text-[#ffe088] font-bold">Contact Us</span>
        </nav>
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 font-headline-lg tracking-tight">Contact Us</h1>
        <p class="text-white/85 text-sm md:text-base max-w-2xl leading-relaxed font-medium">Get in touch with our team for bulk inquiries, custom specifications, or technical support</p>
    </div>
</section>

<!-- Reopen centered wrapper for page content -->
<div class="max-w-[1700px] mx-auto w-full flex-1 flex flex-col relative">

<!-- Contact Layout -->
<main class="w-full px-4 md:px-8 py-14 bg-gradient-to-b from-[#f8f9fc] via-[#f0f4fc] to-[#f8f9fc]">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    
    <!-- Quick Contact Cards Row (Item 11) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- Call Us -->
        <div class="bg-white border border-outline-variant/20 rounded-2xl p-6 flex items-center gap-[18px] hover:shadow-md hover:-translate-y-1 transition-all group relative z-20">
            <div class="w-14 h-14 rounded-full bg-secondary-container/10 group-hover:bg-secondary-container/20 flex items-center justify-center shrink-0 transition-colors">
                <span class="material-symbols-outlined text-secondary text-2xl">call</span>
            </div>
            <div class="flex-1">
                <h3 class="text-xs font-bold text-outline uppercase tracking-wider">Call Us</h3>
                <div class="text-primary font-extrabold text-[13px] sm:text-sm mt-1.5 flex flex-col gap-1">
                    <?php 
                    $phones = explode(',', $settings['phone'] ?? '');
                    foreach ($phones as $phIdx => $ph): 
                        $ph = trim($ph);
                        if (!empty($ph)):
                    ?>
                    <a href="tel:<?php echo htmlspecialchars($ph); ?>" class="hover:text-secondary transition-colors <?php echo $phIdx > 0 ? 'border-t border-outline-variant/20 pt-1 mt-1' : ''; ?>"><?php echo htmlspecialchars($ph); ?></a>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>

        <!-- Email Us -->
        <div class="bg-white border border-outline-variant/20 rounded-2xl p-6 flex items-center gap-[18px] hover:shadow-md hover:-translate-y-1 transition-all group relative z-20">
            <div class="w-14 h-14 rounded-full bg-secondary-container/10 group-hover:bg-secondary-container/20 flex items-center justify-center shrink-0 transition-colors">
                <span class="material-symbols-outlined text-secondary text-2xl">mail</span>
            </div>
            <div class="flex-1">
                <h3 class="text-xs font-bold text-outline uppercase tracking-wider">Email Us</h3>
                <div class="text-primary font-extrabold text-[13px] sm:text-sm mt-1.5 flex flex-col gap-1.5 break-all">
                    <!-- General Inquiry -->
                    <?php if (!empty($settings['email'])): ?>
                    <div class="flex flex-col">
                        <span class="text-[9px] text-outline/80 uppercase tracking-widest font-bold">For Inquiry</span>
                        <a href="mailto:<?php echo htmlspecialchars(trim($settings['email'])); ?>" class="hover:text-secondary transition-colors"><?php echo htmlspecialchars(trim($settings['email'])); ?></a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Sales Inquiry -->
                    <?php if (!empty($settings['email_2'])): ?>
                    <div class="flex flex-col border-t border-outline-variant/20 pt-1.5">
                        <span class="text-[9px] text-outline/80 uppercase tracking-widest font-bold">Sales Inquiry</span>
                        <a href="mailto:<?php echo htmlspecialchars(trim($settings['email_2'])); ?>" class="hover:text-secondary transition-colors"><?php echo htmlspecialchars(trim($settings['email_2'])); ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Visit Office -->
        <div class="bg-white border border-outline-variant/20 rounded-2xl p-6 flex items-center gap-[18px] hover:shadow-md hover:-translate-y-1 transition-all group">
            <div class="w-14 h-14 rounded-full bg-secondary-container/10 group-hover:bg-secondary-container/20 flex items-center justify-center shrink-0 transition-colors">
                <span class="material-symbols-outlined text-secondary text-2xl">location_on</span>
            </div>
            <div class="flex-1">
                <h3 class="text-xs font-bold text-outline uppercase tracking-wider">Visit Office</h3>
                <p class="text-primary font-extrabold text-xs leading-tight mt-1 select-none">Our Branch</p>
                <?php if (!empty($settings['address'])): ?>
                <p class="text-outline font-semibold text-[11px] leading-relaxed mt-1.5"><?php echo nl2br(htmlspecialchars($settings['address'])); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">

        <!-- Left Info Panel -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-primary rounded-2xl p-8 text-white">
                <h2 class="text-xl font-bold mb-3 uppercase tracking-wider text-secondary-fixed">Delivering Convenience, Freedom and Peace of Mind</h2>
                <p class="text-white/60 text-xs font-semibold mb-8 uppercase tracking-widest">Our Global Contact Grid</p>
                
                <div class="space-y-8">
                    <!-- Uniglobe Overseas Unified Contact Info -->
                    <div>
                        <div class="text-sm font-extrabold uppercase tracking-widest text-[#ffe088] mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-[#ffe088]">domain</span> OUR BRANCH
                        </div>
                        <div class="space-y-4">
                            <?php if (!empty($settings['address'])): ?>
                            <div class="flex gap-[15px] items-start">
                                <span class="material-symbols-outlined text-[#ffe088] text-[20px] mt-0.5 shrink-0">location_on</span>
                                <div class="text-sm text-white/85 leading-relaxed font-medium"><?php echo htmlspecialchars($settings['address']); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($settings['phone'])): ?>
                            <div class="flex gap-[15px] items-start">
                                <span class="material-symbols-outlined text-[#ffe088] text-[20px] shrink-0 mt-0.5">call</span>
                                <div class="text-sm text-white/85 leading-relaxed font-medium flex flex-col gap-1.5">
                                    <?php 
                                    $nums = explode(',', $settings['phone']);
                                    foreach ($nums as $num):
                                        $num = trim($num);
                                        if (!empty($num)):
                                    ?>
                                    <a href="tel:<?php echo htmlspecialchars($num); ?>" class="hover:text-[#ffe088] transition-colors relative z-20 flex items-center gap-1.5">
                                        <?php echo htmlspecialchars($num); ?>
                                    </a>
                                    <?php endif; endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($settings['email'])): ?>
                            <div class="flex gap-[15px] items-start">
                                <span class="material-symbols-outlined text-[#ffe088] text-[20px] shrink-0 mt-0.5">mail</span>
                                <div class="text-sm text-white/85 leading-relaxed font-medium flex flex-col gap-1.5">
                                    <a href="mailto:<?php echo htmlspecialchars(trim($settings['email'])); ?>" class="hover:text-[#ffe088] transition-colors relative z-20">
                                        <span class="text-[9px] text-[#ffe088]/80 uppercase tracking-widest font-extrabold block">For Inquiry</span>
                                        <?php echo htmlspecialchars(trim($settings['email'])); ?>
                                    </a>
                                    
                                    <?php if (!empty($settings['email_2'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars(trim($settings['email_2'])); ?>" class="hover:text-[#ffe088] transition-colors relative z-20 border-t border-white/10 pt-1.5">
                                        <span class="text-[9px] text-[#ffe088]/80 uppercase tracking-widest font-extrabold block">Sales Inquiry</span>
                                        <?php echo htmlspecialchars(trim($settings['email_2'])); ?>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Social -->
                <div class="border-t border-white/10 pt-6 mt-8">
                    <div class="text-xs text-white/50 uppercase tracking-widest mb-4 font-bold">Follow Us</div>
                    <div class="flex flex-wrap gap-2.5">
                        <?php
                        $socials = [
                            'facebook'=>['icon'=>'<svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M9 8H7v3h2v9h3v-9h3l.5-3H12V6c0-.88.39-1 1-1h2V2h-3c-2.5 0-4 1.5-4 4v2z"/></svg>','label'=>'Facebook'],
                            'instagram'=>['icon'=>'<svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 1 0 12.324 6.162 6.162 0 0 1 0-12.324zM12 16a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>','label'=>'Instagram'],
                            'linkedin'=>['icon'=>'<svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>','label'=>'LinkedIn'],
                            'twitter'=>['icon'=>'<svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>','label'=>'Twitter/X'],
                            'youtube'=>['icon'=>'<svg class="w-3.5 h-3.5 fill-white" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 0 0-2.11-2.108C19.516 3.53 12 3.53 12 3.53s-7.516 0-9.388.525a3.003 3.003 0 0 0-2.11 2.108C0 8.055 0 12 0 12s0 3.945.502 5.837a3.003 3.003 0 0 0 2.11 2.108c1.872.525 9.388.525 9.388.525s7.516 0 9.388-.525a3.003 3.003 0 0 0 2.11-2.108C24 15.945 24 12 24 12s0-3.945-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>','label'=>'YouTube'],
                        ];
                        foreach ($socials as $key=>$props) {
                            if (!empty($settings[$key])):
                        ?>
                        <a href="<?php echo htmlspecialchars($settings[$key]); ?>" target="_blank" rel="noopener"
                           title="<?php echo $props['label']; ?>"
                           class="w-9 h-9 rounded-full border border-white/20 hover:border-white bg-white/5 hover:bg-white/20 flex items-center justify-center transition-all hover:translate-y-[-3px] relative z-20">
                           <?php echo $props['icon']; ?>
                        </a>
                        <?php endif; } ?>
                    </div>
                </div>
            </div>

            <!-- Business Hours (Item 7) -->
            <div class="bg-surface-container-low border border-outline-variant/20 rounded-2xl p-8 transition-all hover:shadow-md">
                <h3 class="font-bold text-primary mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined text-[26px] text-secondary">schedule</span>
                    <span class="text-lg">Business Hours</span>
                </h3>
                <div class="space-y-4 text-sm font-semibold">
                    <div class="flex justify-between items-center text-on-surface-variant pb-3 border-b border-outline-variant/15 hover:translate-x-1 transition-transform">
                        <span>Monday – Friday</span>
                        <span class="text-primary bg-primary/5 px-3 py-1 rounded-lg">9:00 AM – 6:00 PM</span>
                    </div>
                    <div class="flex justify-between items-center text-on-surface-variant pb-3 border-b border-outline-variant/15 hover:translate-x-1 transition-transform">
                        <span>Saturday</span>
                        <span class="text-primary bg-primary/5 px-3 py-1 rounded-lg">9:00 AM – 1:00 PM</span>
                    </div>
                    <div class="flex justify-between items-center text-on-surface-variant hover:translate-x-1 transition-transform">
                        <span>Sunday</span>
                        <span class="text-outline bg-surface-container-high px-3 py-1 rounded-lg">Closed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enquiry Form -->
        <div class="lg:col-span-3">
            <div class="bg-white border border-outline-variant/20 rounded-2xl p-8 shadow-[0_8px_30px_rgb(0,0,0,0.02)]" id="enquiry-form-card">
                <h2 class="text-2xl font-bold text-primary mb-2">Send an Enquiry</h2>
                <p class="text-on-surface-variant text-sm mb-8">Fill in the form below and our team will get back to you within 24 hours.</p>

                <div id="form-success" class="hidden bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4 mb-6 flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-green-600">check_circle</span>
                    Thank you! Your enquiry has been sent. We'll contact you shortly.
                </div>
                <div id="form-error" class="hidden bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4 mb-6 flex items-center gap-3 text-sm font-medium">
                    <span class="material-symbols-outlined text-red-600">error</span>
                    <span id="form-error-msg">Something went wrong. Please try again.</span>
                </div>

                <form id="enquiry-form" method="POST" action="contact.php" class="space-y-5" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-2" for="cf-name">Full Name *</label>
                        <input id="cf-name" name="name" type="text" required placeholder="John Smith"
                               class="w-full h-[54px] border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-[#0a3d75] focus:ring-4 focus:ring-[#0a3d75]/10 transition-all bg-surface-container-low" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-2" for="cf-email">Email Address *</label>
                            <input id="cf-email" name="email" type="email" required placeholder="you@company.com"
                                   class="w-full h-[54px] border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-[#0a3d75] focus:ring-4 focus:ring-[#0a3d75]/10 transition-all bg-surface-container-low" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-2" for="cf-phone">Phone Number</label>
                            <input id="cf-phone" name="phone" type="tel" pattern="[\\+0-9\\-\\s()]+" title="Valid phone number" placeholder="+91 98765 43210"
                                   class="w-full h-[54px] border border-[#dbe2ee] rounded-xl px-4 text-sm focus:outline-none focus:border-[#0a3d75] focus:ring-4 focus:ring-[#0a3d75]/10 transition-all bg-surface-container-low" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-2" for="cf-message">Message *</label>
                        <textarea id="cf-message" name="message" rows="5" required placeholder="Describe your requirement, product codes, quantities, or any specific technical questions..."
                                  class="w-full border border-[#dbe2ee] rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:border-[#0a3d75] focus:ring-4 focus:ring-[#0a3d75]/10 transition-all bg-surface-container-low resize-none"><?php echo htmlspecialchars($prefilled_msg); ?></textarea>
                    </div>
                    
                    <button type="submit" id="submit-btn"
                            class="w-full bg-gradient-to-r from-primary to-primary-container hover:from-secondary-container hover:to-secondary-fixed hover:text-on-secondary-container text-white py-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2.5 shadow-md hover:shadow-lg hover:-translate-y-0.5 relative z-20">
                        <span class="material-symbols-outlined text-[20px]">send</span>
                        <span id="submit-label">Send Enquiry</span>
                    </button>
                    
                    <!-- Trust line (Item 12) -->
                    <p class="text-center text-outline text-xs font-semibold mt-3 flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px] text-secondary">schedule</span>
                        We typically respond within 24 hours.
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Google Map Section (Item 10) -->
    <div class="mt-16 border-t border-outline-variant/20 pt-16">
        <div class="text-center mb-10">
            <p class="text-secondary font-bold text-xs uppercase tracking-widest mb-3">Our Location</p>
            <h2 class="text-3xl font-extrabold text-primary mb-2">Find Us Globally</h2>
            <div class="h-1 w-14 bg-secondary-container rounded-full mx-auto"></div>
        </div>
        <div class="w-full h-[450px] rounded-3xl overflow-hidden border border-outline-variant/20 shadow-lg relative group">
            <?php
            $map_src = !empty($settings['map_embed_url'])
                ? htmlspecialchars($settings['map_embed_url'])
                : 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3683.829141014902!2d70.04398107604543!3d22.45424563728639!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39571542f567cf17%3A0xe54fb72dcf351989!2sG.I.D.C.%20Phase%203%2C%20Dared%2C%20Jamnagar%2C%20Gujarat%20361004!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin';
            ?>
            <iframe class="w-full h-full border-0 grayscale hover:grayscale-0 transition-all duration-700"
                    src="<?php echo $map_src; ?>"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</main>

<script>
document.getElementById('enquiry-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form    = e.target;
    const btn     = document.getElementById('submit-btn');
    const label   = document.getElementById('submit-label');
    const success = document.getElementById('form-success');
    const errBox  = document.getElementById('form-error');

    // Basic validation
    const nameEl = document.getElementById('cf-name');
    const emailEl = document.getElementById('cf-email');
    const phoneEl = document.getElementById('cf-phone');
    const messageEl = document.getElementById('cf-message');

    let valid = true;
    [nameEl, emailEl, phoneEl, messageEl].forEach(el => {
        if (el) {
            el.style.borderColor = '';
            if (el.hasAttribute('required') && !el.value.trim()) {
                el.style.borderColor = '#ba1a1a';
                valid = false;
            }
        }
    });

    if (!valid) return;

    // Validate email has correct name@email.com format
    const email = emailEl.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        emailEl.style.borderColor = '#ba1a1a';
        document.getElementById('form-error-msg').textContent = 'Please enter a valid email address (e.g., name@email.com).';
        errBox.classList.remove('hidden');
        return;
    }

    // Validate phone number is 10 digits
    const rawPhone = phoneEl.value.trim();
    const cleanPhone = rawPhone.replace(/[^0-9]/g, '');
    if (cleanPhone.length !== 10) {
        phoneEl.style.borderColor = '#ba1a1a';
        document.getElementById('form-error-msg').textContent = 'Please enter a valid 10-digit mobile number.';
        errBox.classList.remove('hidden');
        return;
    }

    btn.disabled = true;
    label.textContent = 'Submitting...';
    errBox.classList.add('hidden');

    // Split name into first and last name
    const fullName = nameEl.value.trim();
    const nameParts = fullName.split(' ');
    const firstName = nameParts[0];
    const lastName = nameParts.slice(1).join(' ');

    try {
        const response = await fetch('<?php echo API_BASE_URL; ?>api/front_api/inquiries.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                type: 'contact',
                first_name: firstName,
                last_name: lastName,
                email: email,
                phone: cleanPhone,
                message: messageEl.value
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            success.classList.remove('hidden');
            form.classList.add('hidden'); // Hide form on successful submit
            form.reset();
        } else {
            document.getElementById('form-error-msg').textContent = result.message || 'Failed to submit enquiry. Please try again.';
            errBox.classList.remove('hidden');
        }
    } catch(err) {
        document.getElementById('form-error-msg').textContent = 'Failed to submit due to network issues. Please email us directly.';
        errBox.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        label.textContent = 'Send Enquiry';
    }
});
</script>

<?php include 'includes/footer.php'; ?>