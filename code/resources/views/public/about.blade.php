@extends('layouts.guest')

@section('title', 'À Propos - PFE Platform')

@section('content')
<div class="min-vh-100 bg-light">
    <!-- Header Section -->
    <section class="bg-primary text-white py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-4 fw-bold mb-4">À Propos de la Plateforme PFE</h1>
                    <p class="lead">Une solution complète pour la gestion des Projets de Fin d'Études</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h2 class="display-5 fw-bold mb-4">Notre Mission</h2>
                    <p class="lead text-muted mb-5">
                        La plateforme PFE a été conçue pour simplifier et optimiser la gestion des Projets de Fin d'Études dans les établissements d'enseignement supérieur. Notre objectif est de faciliter la collaboration entre étudiants, enseignants et administration.
                    </p>

                    <h2 class="display-5 fw-bold mb-4">Fonctionnalités Principales</h2>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <h3 class="h4 fw-semibold mb-3">Pour les Étudiants</h3>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Consultation des sujets disponibles</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Formation d'équipes</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Soumission de livrables</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Suivi du projet</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h3 class="h4 fw-semibold mb-3">Pour les Enseignants</h3>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Proposition de sujets</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Encadrement d'équipes</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Évaluation des livrables</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Planification des soutenances</li>
                            </ul>
                        </div>
                    </div>

                    @if($teacher)
                    <h2 class="display-5 fw-bold mb-4">Responsable de la Plateforme</h2>
                    <div class="card mb-5">
                        <div class="card-body">
                            <h3 class="card-title h5">{{ $teacher->name }}</h3>
                            <p class="card-text text-muted mb-3">{{ $teacher->email }}</p>
                            @if($teacher->bio)
                            <p class="card-text">{{ $teacher->bio }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <h2 class="display-5 fw-bold mb-4">Contactez-nous</h2>
                    <p class="text-muted mb-4">
                        Pour toute question concernant la plateforme ou les projets PFE, n'hésitez pas à nous contacter.
                    </p>
                    <div class="mb-3">
                        <p class="mb-2">
                            <span class="fw-semibold">Email:</span> info@pfe.edu
                        </p>
                        <p class="mb-2">
                            <span class="fw-semibold">Département:</span> Informatique
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection