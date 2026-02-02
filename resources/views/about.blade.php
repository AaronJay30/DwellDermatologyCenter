@extends('layouts.patient')

@section('content')
<!-- About Us Section -->
<section class="about-section" style="padding: 80px 0; background-color: var(--white);">
    <div class="container">
        <div class="about-content" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">
            <!-- Header -->
            <div class="about-header" style="text-align: center; margin-bottom: 60px;">
                <h1 style="font-size: 3rem; font-weight: bold; color: var(--primary-color); margin-bottom: 20px; font-family: serif;">About Dr. Dianne Paras</h1>
                <div style="width: 100px; height: 4px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); margin: 0 auto; border-radius: 2px;"></div>
            </div>

            <!-- Doctor Profile -->
            <div class="doctor-profile">
                <!-- Doctor Photo -->
                <div class="doctor-photo">
                    <div class="doctor-photo-container">
                        <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&h=400&fit=crop&crop=face" alt="Dr. Dianne Paras" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="margin-top: 30px;">
                        <h3 style="font-size: 1.5rem; font-weight: bold; color: var(--black); margin-bottom: 10px;">Dr. Dianne Paras</h3>
                        <p style="color: var(--primary-color); font-size: 1.1rem; font-weight: 500;">Chief Dermatologist & Founder</p>
                    </div>
                </div>

                <!-- Doctor Information -->
                <div class="doctor-info">
                    <div class="doctor-info-box">
                        <h2 style="font-size: 2rem; font-weight: bold; color: var(--black); margin-bottom: 30px;">Meet Our Founder</h2>
                        
                        <div style="font-size: 1.1rem; line-height: 1.8; color: var(--dark-text);">
                            <p style="margin-bottom: 25px;">
                                Dr. Dianne Paras is a board-certified dermatologist with over 15 years of experience in medical and cosmetic dermatology. She founded Dwell Dermatology Center with a vision to provide personalized, comprehensive skin care that combines the latest medical advances with a warm, patient-centered approach.
                            </p>
                            
                            <p style="margin-bottom: 25px;">
                                After completing her medical degree at the University of the Philippines and her dermatology residency at the Philippine General Hospital, Dr. Paras pursued advanced training in cosmetic dermatology in the United States. Her expertise spans across acne treatment, anti-aging therapies, skin cancer screening, and advanced laser procedures.
                            </p>
                            
                            <p style="margin-bottom: 25px;">
                                Dr. Paras believes that healthy, beautiful skin is achievable for everyone through proper education, personalized treatment plans, and cutting-edge technology. She is passionate about empowering her patients with knowledge about their skin and providing them with the most effective, safe, and innovative treatments available.
                            </p>
                            
                            <p>
                                When not treating patients, Dr. Paras enjoys spending time with her family, practicing yoga, and staying updated with the latest developments in dermatology through continuous education and research.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="mission-vision">
                <div class="mission">
                    <h3 class="mission-title">
                        <i class="fas fa-bullseye" style="font-size: 2rem;"></i>
                        <span>Our Mission</span>
                    </h3>
                    <p style="font-size: 1.1rem; line-height: 1.7; color: var(--dark-text);">
                        To provide exceptional dermatological care that enhances both the health and beauty of our patients' skin, using the most advanced techniques and personalized treatment approaches in a comfortable, professional environment.
                    </p>
                </div>

                <div class="vision">
                    <h3 class="vision-title">
                        <i class="fas fa-eye" style="font-size: 2rem;"></i>
                        <span>Our Vision</span>
                    </h3>
                    <p style="font-size: 1.1rem; line-height: 1.7; color: var(--dark-text);">
                        To be the leading dermatology center in the region, recognized for our commitment to excellence, innovation, and patient satisfaction while maintaining the highest standards of medical care and ethical practice.
                    </p>
                </div>
            </div>

            <!-- Values -->
            <div class="values" style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: 2.5rem; font-weight: bold; color: var(--black); margin-bottom: 50px;">Our Core Values</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
                    <div class="value-item" style="padding: 30px; background: var(--white); border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <i class="fas fa-heart" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 20px;"></i>
                        <h4 style="font-size: 1.3rem; font-weight: bold; color: var(--black); margin-bottom: 15px;">Compassion</h4>
                        <p style="color: var(--gray-dark); line-height: 1.6;">We treat every patient with empathy, understanding, and genuine care for their well-being.</p>
                    </div>

                    <div class="value-item" style="padding: 30px; background: var(--white); border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <i class="fas fa-graduation-cap" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 20px;"></i>
                        <h4 style="font-size: 1.3rem; font-weight: bold; color: var(--black); margin-bottom: 15px;">Excellence</h4>
                        <p style="color: var(--gray-dark); line-height: 1.6;">We maintain the highest standards of medical practice and continuously pursue professional development.</p>
                    </div>

                    <div class="value-item" style="padding: 30px; background: var(--white); border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <i class="fas fa-lightbulb" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 20px;"></i>
                        <h4 style="font-size: 1.3rem; font-weight: bold; color: var(--black); margin-bottom: 15px;">Innovation</h4>
                        <p style="color: var(--gray-dark); line-height: 1.6;">We embrace the latest technologies and treatment methods to provide the best possible care.</p>
                    </div>

                    <div class="value-item" style="padding: 30px; background: var(--white); border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                        <i class="fas fa-handshake" style="font-size: 3rem; color: var(--primary-color); margin-bottom: 20px;"></i>
                        <h4 style="font-size: 1.3rem; font-weight: bold; color: var(--black); margin-bottom: 15px;">Trust</h4>
                        <p style="color: var(--gray-dark); line-height: 1.6;">We build lasting relationships based on honesty, transparency, and mutual respect.</p>
                    </div>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="cta-section">
                <h2 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 20px;">Ready to Start Your Skin Journey?</h2>
                <p style="font-size: 1.2rem; margin-bottom: 40px; opacity: 0.9;">Book a consultation with Dr. Paras and discover the personalized care that will transform your skin.</p>
                <div class="cta-buttons">
                    <a href="{{ route('consultations.create') }}" class="btn btn-primary">
                        Book Consultation
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Doctor Profile Styles */
.doctor-profile {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 60px;
    align-items: center;
    margin-bottom: 80px;
}

.doctor-photo {
    text-align: center;
}

.doctor-photo-container {
    width: 300px;
    height: 300px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    border: 8px solid var(--white);
}

.doctor-info-box {
    background: linear-gradient(135deg, var(--teal-light), var(--teal-medium));
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

/* Mission & Vision Styles */
.mission-vision {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-bottom: 80px;
}

.mission, .vision {
    background: var(--white);
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.mission {
    border-left: 5px solid var(--primary-color);
}

.vision {
    border-left: 5px solid var(--accent-color);
}

.mission-title, .vision-title {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.mission-title {
    color: var(--primary-color);
}

.vision-title {
    color: var(--accent-color);
}

/* CTA Section Styles */
.cta-section {
    text-align: center;
    background: linear-gradient(135deg, var(--primary-color), #1a6b7a);
    padding: 60px 40px;
    border-radius: 20px;
    color: white;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 15px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-primary {
    background: white;
    color: var(--primary-color);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn-secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.value-item:hover {
    transform: translateY(-10px);
}

/* Tablet Responsive */
@media (max-width: 768px) {
    .doctor-profile {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .doctor-photo-container {
        width: 250px;
        height: 250px;
    }
    
    .doctor-info-box {
        padding: 30px;
    }
    
    .doctor-info-box h2 {
        font-size: 1.8rem !important;
        margin-bottom: 20px !important;
    }
    
    .doctor-info-box div p {
        font-size: 1rem !important;
        line-height: 1.6 !important;
    }
    
    .mission-vision {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .mission, .vision {
        padding: 30px;
    }
    
    .mission-title, .vision-title {
        font-size: 1.5rem;
    }
    
    .mission p, .vision p {
        font-size: 1rem !important;
    }
    
    .about-header h1 {
        font-size: 2.5rem !important;
    }
    
    .values h2 {
        font-size: 2rem !important;
    }
    
    .value-item {
        padding: 20px !important;
    }
    
    .value-item i {
        font-size: 2.5rem !important;
    }
    
    .value-item h4 {
        font-size: 1.2rem !important;
    }
    
    .cta-section {
        padding: 40px 20px;
    }
    
    .cta-section h2 {
        font-size: 2rem !important;
    }
    
    .cta-section p {
        font-size: 1.1rem !important;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }
    
    .btn {
        padding: 12px 25px;
        font-size: 1rem;
        width: 100%;
        max-width: 280px;
    }
}

/* Mobile Responsive */
@media (max-width: 480px) {
    .about-section {
        padding: 40px 0 !important;
    }
    
    .about-content {
        padding: 0 15px !important;
    }
    
    .about-header {
        margin-bottom: 40px !important;
    }
    
    .about-header h1 {
        font-size: 1.75rem !important;
    }
    
    .doctor-profile {
        margin-bottom: 50px !important;
        gap: 30px;
    }
    
    .doctor-photo-container {
        width: 200px;
        height: 200px;
        border: 5px solid var(--white);
    }
    
    .doctor-photo h3 {
        font-size: 1.25rem !important;
        margin-top: 20px !important;
    }
    
    .doctor-photo p {
        font-size: 1rem !important;
    }
    
    .doctor-info-box {
        padding: 25px !important;
    }
    
    .doctor-info-box h2 {
        font-size: 1.5rem !important;
        margin-bottom: 15px !important;
    }
    
    .doctor-info-box div p {
        font-size: 0.95rem !important;
        line-height: 1.6 !important;
        margin-bottom: 15px !important;
    }
    
    .mission-vision {
        margin-bottom: 50px !important;
        gap: 25px;
    }
    
    .mission, .vision {
        padding: 25px !important;
    }
    
    .mission-title, .vision-title {
        font-size: 1.3rem !important;
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    
    .mission-title i, .vision-title i {
        font-size: 1.75rem !important;
    }
    
    .mission p, .vision p {
        font-size: 0.95rem !important;
        line-height: 1.6 !important;
        text-align: center;
    }
    
    .values {
        margin-bottom: 40px !important;
    }
    
    .values h2 {
        font-size: 1.75rem !important;
        margin-bottom: 30px !important;
    }
    
    .value-item {
        padding: 20px !important;
    }
    
    .value-item i {
        font-size: 2.25rem !important;
        margin-bottom: 15px !important;
    }
    
    .value-item h4 {
        font-size: 1.1rem !important;
        margin-bottom: 10px !important;
    }
    
    .value-item p {
        font-size: 0.9rem !important;
        line-height: 1.5 !important;
    }
    
    .cta-section {
        padding: 30px 20px !important;
    }
    
    .cta-section h2 {
        font-size: 1.65rem !important;
        margin-bottom: 15px !important;
    }
    
    .cta-section p {
        font-size: 1rem !important;
        margin-bottom: 30px !important;
    }
    
    .cta-buttons {
        gap: 12px;
    }
    
    .btn {
        padding: 12px 20px;
        font-size: 0.95rem;
    }
}

/* Extra Small Mobile */
@media (max-width: 360px) {
    .about-header h1 {
        font-size: 1.5rem !important;
    }
    
    .doctor-photo-container {
        width: 180px;
        height: 180px;
    }
    
    .doctor-info-box {
        padding: 20px !important;
    }
    
    .mission, .vision {
        padding: 20px !important;
    }
    
    .values h2 {
        font-size: 1.5rem !important;
    }
    
    .cta-section h2 {
        font-size: 1.5rem !important;
    }
}
</style>
@endsection
