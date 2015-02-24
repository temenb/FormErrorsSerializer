Forked from https://gist.github.com/Graceas/6505663

# Create service
 add service to app/config/services.yml

```Go
services:
    form_errors_serializer:
        class: FormErrorsSerializer\FormErrorsSerializer
```
# In action you may use

```Go
$ajax = $request->isXmlHttpRequest();
if ($request->getMethod() == 'POST') {
   $form->handleRequest($request);
   if ($form->isValid()) {
         // ...
   } else {
       if ($ajax) {
           $errors = $this->get('form_errors_serializer')->serializeFormErrors($form, true, true);
           return new Response(json_encode(array(
               'status' => 'error',
               'errors' => $errors
           )));
       }
   }
}
```