@extends('layouts.patient')

@section('content')
<!-- About Us Section -->
<section class="about-section" style="padding: 80px 0; background-color: var(--white);">
    <div class="container">
        <div class="about-content" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">
            <!-- Header -->
            <div class="about-header" style="text-align: center; margin-bottom: 60px;">
                <h1 style="font-size: 3rem; font-weight: bold; color: var(--primary-color); margin-bottom: 20px; font-family: serif;">About Dr. Dianne Paraz</h1>
                <div style="width: 100px; height: 4px; background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); margin: 0 auto; border-radius: 2px;"></div>
            </div>

            <!-- Doctor Profile -->
            <div class="doctor-profile" style="display: grid; grid-template-columns: 1fr 2fr; gap: 60px; align-items: center; margin-bottom: 80px;">
                <!-- Doctor Photo -->
                <div class="doctor-photo" style="text-align: center;">
                    <div style="width: 300px; height: 300px; border-radius: 50%; overflow: hidden; margin: 0 auto; box-shadow: 0 20px 40px rgba(0,0,0,0.1); border: 8px solid var(--white);">
                        <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?w=400&h=400&fit=crop&crop=face" alt="Dr. Dianne Paraz" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="margin-top: 30px;">
                        <h3 style="font-size: 1.5rem; font-weight: bold; color: var(--black); margin-bottom: 10px;">Dr. Dianne Paraz</h3>
                        <p style="color: var(--primary-color); font-size: 1.1rem; font-weight: 500;">Chief Dermatologist & Founder</p>
                    </div>
                </div>

                <!-- Doctor Information -->
                <div class="doctor-info">
                    <div style="background: linear-gradient(135deg, var(--teal-light), var(--teal-medium)); padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <h2 style="font-size: 2rem; font-weight: bold; color: var(--black); margin-bottom: 30px;">Meet Our Founder</h2>
                        
                        <div style="font-size: 1.1rem; line-height: 1.8; color: var(--dark-text);">
                            <p style="margin-bottom: 25px;">
                                Dr. Dianne Paraz is a board-certified dermatologist with over 15 years of experience in medical and cosmetic dermatology. She founded Dwell Dermatology Center with a vision to provide personalized, comprehensive skin care that combines the latest medical advances with a warm, patient-centered approach.
                            </p>
                            
                            <p style="margin-bottom: 25px;">
                                After completing her medical degree at the University of the Philippines and her dermatology residency at the Philippine General Hospital, Dr. Paraz pursued advanced training in cosmetic dermatology in the United States. Her expertise spans across acne treatment, anti-aging therapies, skin cancer screening, and advanced laser procedures.
                            </p>
                            
                            <p style="margin-bottom: 25px;">
                                Dr. Paraz believes that healthy, beautiful skin is achievable for everyone through proper education, personalized treatment plans, and cutting-edge technology. She is passionate about empowering her patients with knowledge about their skin and providing them with the most effective, safe, and innovative treatments available.
                            </p>
                            
                            <p>
                                When not treating patients, Dr. Paraz enjoys spending time with her family, practicing yoga, and staying updated with the latest developments in dermatology through continuous education and research.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mission & Vision -->
            <div class="mission-vision" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 80px;">
                <div class="mission" style="background: var(--white); padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-left: 5px solid var(--primary-color);">
                    <h3 style="font-size: 1.8rem; font-weight: bold; color: var(--primary-color); margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
                        <i class="fas fa-bullseye" style="font-size: 2rem;"></i>
                        Our Mission
                    </h3>
                    <p style="font-size: 1.1rem; line-height: 1.7; color: var(--dark-text);">
                        To provide exceptional dermatological care that enhances both the health and beauty of our patients' skin, using the most advanced techniques and personalized treatment approaches in a comfortable, professional environment.
                    </p>
                </div>

                <div class="vision" style="background: var(--white); padding: 40px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-left: 5px solid var(--accent-color);">
                    <h3 style="font-size: 1.8rem; font-weight: bold; color: var(--accent-color); margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
                        <i class="fas fa-eye" style="font-size: 2rem;"></i>
                        Our Vision
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
            <div class="cta-section" style="text-align: center; background: linear-gradient(135deg, var(--primary-color), #1a6b7a); padding: 60px 40px; border-radius: 20px; color: white;">
                <h2 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 20px;">Ready to Start Your Skin Journey?</h2>
                <p style="font-size: 1.2rem; margin-bottom: 40px; opacity: 0.9;">Book a consultation with Dr. Paraz and discover the personalized care that will transform your skin.</p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('consultations.create') }}" class="btn" style="background: white; color: var(--primary-color); padding: 15px 30px; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                        Book Consultation
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn" style="background: transparent; color: white; padding: 15px 30px; border: 2px solid white; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease;">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.value-item:hover {
    transform: translateY(-10px);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

@media (max-width: 768px) {
    .doctor-profile {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }
    
    .mission-vision {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .about-header h1 {
        font-size: 2.5rem;
    }
    
    .doctor-photo div {
        width: 250px;
        height: 250px;
    }
    
    .cta-section h2 {
        font-size: 2rem;
    }
    
    .cta-section .btn {
        display: block;
        margin: 10px auto;
        width: fit-content;
    }
}
</style>
@endsection
