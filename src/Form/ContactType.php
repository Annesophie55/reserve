<?php
namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
              'label' => 'Veuillez indiquer votre nom complet : '
            ])
            ->add('email',EmailType::class,[
              'label' => 'Veuillez renseigner votre email : '
            ])
            ->add('subject',TextType::class,[
              'label' => 'Veuillez renseigner le sujet du message : '
            ])
            ->add('content', TextareaType::class, [
                'label' =>'Votre message : ',
                'attr' => ['rows' => 6],
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}