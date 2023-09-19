<?php

namespace App\Form;

use App\Entity\Rdv;
use App\Entity\Note;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class NoteType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
        $resolver->setDefined(['user']);
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userId = $options['user']->getId(); // Assurez-vous que $options['user'] existe et est un objet User
        $builder
            ->add('Content',TextareaType::class, [
                'label' => 'Ajouter une note de suivi de rendez-vous : ',
                'attr' => [
                'rows' => 15, // Nombre de lignes
                'cols' => 40, // Nombre de colonnes
    ],
            ])
            ->add('userId', HiddenType::class, [
                'data' => $userId,
                'mapped' => false
            ])
        ;
    }
    
    

}
