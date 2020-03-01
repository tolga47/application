<?php

namespace App\Controller\Api;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/order")
 */
class ApiOrderController extends AbstractController
{

    /**
     * @Route("/all", name="api_order_all", methods={"GET"})
     * @return JsonResponse
     */
    public function all()
    {   
        $entityManager = $this->getDoctrine()->getManager();
        $productRepository = $entityManager->getRepository(Product::class);

        $orders = $productRepository->findAll();

        if (!$orders) {
            return new JsonResponse(["error" =>'No order found'], 500);
        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $json = $serializer->serialize($orders, 'json');

        return new JsonResponse($json, 200);
    }

    /**
     * @Route("/{id}", name="api_order_detail", methods={"GET"})
     * @return JsonResponse
     */
    public function detail($id)
    {   
        $order = $this->getDoctrine()
        ->getRepository(Product::class)
        ->find($id);
        
        if (!$order) {
            return new JsonResponse(["error" =>'No order found for id'], 500);
        }

        return new JsonResponse($this->serialize($order), 200);
    }

    /**
     * @Route("/create", name="api_order_create",  methods={"POST"})
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function create(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        $validator = Validation::createValidator();

        $constraint = new Assert\Collection(array(
            // the keys correspond to the keys in the input array
            'orderCode' => new Assert\Length(array('min' => 1)),
            'productid' => new Assert\Length(array('min' => 1)),
            'quantity' => new Assert\NotBlank(),
            'address' => new Assert\NotBlank(),
        ));

        $violations = $validator->validate($data, $constraint);

        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], 500);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $order = new Product();
        $order
            ->setOrderCode($data['orderCode'])
            ->setProductid($data['productid'])
            ->setQuantity($data['quantity'])
            ->setAddress($data['address'])
            ->setShippingDate(\DateTime::createFromFormat('Y-m-d', '0000-00-00'))
        ;

        try {
            $entityManager->persist($order);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }

        return new JsonResponse(["success" => "Order has been created!"], 200);
    }

    /**
     * @Route("/update", name="api_order_update",  methods={"POST"})
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function update(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        $validator = Validation::createValidator();

        
        
        $entityManager = $this->getDoctrine()->getManager();
        $order = $entityManager->getRepository(Product::class)->find($data['id']);
        if (!$order) {
            return new JsonResponse(["error" =>'No order found for id'], 500);
        }

        $shippingDate = $order->getShippingDate()->format('Y-m-d');
        if($shippingDate != '-0001-11-30') {
            return new JsonResponse(["error" =>'Order already shipped!'], 500);
        }
        
        $order
            ->setOrderCode($data['orderCode'])
            ->setProductid($data['productid'])
            ->setQuantity($data['quantity'])
            ->setAddress($data['address'])
            ->setShippingDate(\DateTime::createFromFormat('Y-m-d', $data['shippingDate']))
        ;

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }

        return new JsonResponse(["success" => "Order has been updated!"], 200);
    }

    protected function serialize(Product $order)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $json = $serializer->serialize($order, 'json');

        return $json;
    }
}
