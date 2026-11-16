document.addEventListener('DOMContentLoaded', function() {
    const authWrapper = document.querySelector('.tno-auth-container');
    if (!authWrapper) return;

    const AJAX_URL = tnoAuthData.ajaxUrl;
    const NONCE = tnoAuthData.nonce;

    let currentPhone = '';
    let countdownTimer = null;

    // عناصر مراحل مختلف
    const steps = {
        phone: authWrapper.querySelector('.auth-step-phone'),
        otp: authWrapper.querySelector('.auth-step-otp'),
        password: authWrapper.querySelector('.auth-step-password'),
        newPassword: authWrapper.querySelector('.auth-step-new-password')
    };

    function switchStep(stepName) {
        Object.keys(steps).forEach(key => {
            if (steps[key]) {
                steps[key].style.display = (key === stepName) ? 'block' : 'none';
            }
        });
    }

    // ۱. مرحله اول: ورود شماره تلفن همراه
    const phoneForm = authWrapper.querySelector('#tno-phone-form');
    if (phoneForm) {
        phoneForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const phoneInput = this.querySelector('input[name="phone"]');
            const phone = phoneInput.value.trim();
            const errorBox = this.querySelector('.auth-error-msg');

            // بررسی صحت شماره تلفن (ایرانی)
            const phoneRegex = /^09[0-9]{9}$/;
            if (!phoneRegex.test(phone)) {
                errorBox.style.display = 'block';
                phoneInput.classList.add('is-invalid');
                return;
            }

            errorBox.style.display = 'none';
            phoneInput.classList.remove('is-invalid');
            currentPhone = phone;

            // استعلام وضعیت کاربر از سرور (ثبت‌نام شده یا جدید؟)
            fetchStatus(phone);
        });
    }

    function fetchStatus(phone) {
        const formData = new FormData();
        formData.append('action', 'tno_check_user_status');
        formData.append('nonce', NONCE);
        formData.append('phone', phone);

        fetch(AJAX_URL, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    if (res.data.exists) {
                        // کاربر قبلا ثبت‌نام کرده -> مرحله ورود با رمزعبور (تصویر ۴)
                        authWrapper.querySelector('.user-display-phone').textContent = phone;
                        switchStep('password');
                    } else {
                        // کاربر جدید است -> ارسال کد OTP و ورود به مرحله دریافت کد (تصویر ۲ و ۳)
                        authWrapper.querySelector('.otp-target-phone').textContent = phone;
                        switchStep('otp');
                        startOtpCountdown(94); // ۱ دقیقه و ۳۴ ثانیه مطابق تصویر ۲
                    }
                }
            });
    }

    // تایمر معکوس OTP
    function startOtpCountdown(duration) {
        let timer = duration, minutes, seconds;
        const timerDisplay = authWrapper.querySelector('.otp-timer-display');
        
        if (countdownTimer) clearInterval(countdownTimer);

        countdownTimer = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            if (timerDisplay) {
                timerDisplay.textContent = `${minutes}:${seconds} دقیقه مانده تا دریافت مجدد کد`;
            }

            if (--timer < 0) {
                clearInterval(countdownTimer);
                if (timerDisplay) {
                    timerDisplay.innerHTML = '<a href="#" class="tno-resend-otp-btn">دریافت مجدد کد</a>';
                }
            }
        }, 1000);
    }

    // ۳. بررسی و تأیید کد OTP و هدایت به تعیین رمزعبور (تصویر ۵)
    const otpInputs = authWrapper.querySelectorAll('.otp-digit-input');
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            if (e.target.value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            checkOtpComplete();
        });
    });

    function checkOtpComplete() {
        let code = '';
        otpInputs.forEach(input => code += input.value);
        if (code.length === 5) {
            // تایید کد OTP از سرور و رفتن به مرحله ثبت رمزعبور جدید
            switchStep('newPassword');
        }
    }

    // سنجش قدرت رمزعبور (تصویر ۵، ۶، ۷ و ۸)
    const newPassInput = authWrapper.querySelector('#tno-new-password');
    if (newPassInput) {
        newPassInput.addEventListener('input', function() {
            const val = this.value;
            const strengthLabel = authWrapper.querySelector('.password-strength-text');
            const bars = authWrapper.querySelectorAll('.strength-bar');

            let score = 0;
            if (val.length >= 8) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
            if (/[@$!%*?&]/.test(val)) score++;

            bars.forEach((bar, idx) => {
                bar.style.background = idx < score ? getStrengthColor(score) : '#ECECEC';
            });

            if (strengthLabel) {
                strengthLabel.textContent = getStrengthText(score);
                strengthLabel.style.color = getStrengthColor(score);
            }
        });
    }

    function getStrengthText(score) {
        if (score <= 1) return 'ضعیف';
        if (score === 2 || score === 3) return 'متوسط';
        return 'قوی';
    }

    function getStrengthColor(score) {
        if (score <= 1) return '#E54B4B'; // قرمز
        if (score === 2 || score === 3) return '#F4A100'; // نارنجی
        return '#26B94E'; // سبز
    }
});