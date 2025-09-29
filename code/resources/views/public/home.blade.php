@extends('layouts.guest')

@section('title', 'PFE Platform - Home')

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-4 fw-bold mb-4">
                        PFE Platform
                    </h1>
                    <p class="lead mb-5">
                        Plateforme de Gestion des Projets de Fin d'Études
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Se Connecter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold mb-4">Fonctionnalités de la Plateforme</h2>
                    <p class="lead text-muted">
                        Gérez efficacement vos projets de fin d'études avec notre plateforme complète
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <div class="text-primary mb-4">
                                <i class="fas fa-file-alt fa-3x"></i>
                            </div>
                            <h3 class="card-title h4">Gestion des Sujets</h3>
                            <p class="card-text text-muted">
                                Proposez, validez et gérez les sujets de PFE avec un workflow complet
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <div class="text-primary mb-4">
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                            <h3 class="card-title h4">Équipes & Projets</h3>
                            <p class="card-text text-muted">
                                Formez des équipes et gérez les projets de A à Z
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <div class="text-primary mb-4">
                                <i class="fas fa-chart-bar fa-3x"></i>
                            </div>
                            <h3 class="card-title h4">Suivi & Soutenances</h3>
                            <p class="card-text text-muted">
                                Suivez les progrès et planifiez les soutenances
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Projects/Subjects Section -->
    @if($featuredProjects->count() > 0)
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold mb-4">Projets en Cours</h2>
                    <p class="lead text-muted">Découvrez les projets actuellement en développement</p>
                </div>
            </div>

            <div class="row g-4">
                @foreach($featuredProjects as $project)
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $project->subject->title ?? 'Projet PFE' }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($project->subject->description ?? 'Description du projet', 150) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">{{ ucfirst($project->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if($latestPosts->count() > 0)
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="display-5 fw-bold mb-4">Sujets Récents</h2>
                    <p class="lead text-muted">Les derniers sujets publiés</p>
                </div>
            </div>

            <div class="row g-4">
                @foreach($latestPosts as $subject)
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $subject->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($subject->description, 150) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success">{{ ucfirst($subject->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="bg-primary text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-4">Prêt à commencer ?</h2>
                    <p class="lead mb-4">Rejoignez notre plateforme PFE dès aujourd'hui</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
