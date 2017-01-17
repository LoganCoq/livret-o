<?php

/* @IUTOLivret/Default/index.html.twig */
class __TwigTemplate_e67615c317a838874e217e9cfc9238621df86a399b949996f11135d0c515e24c extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'body' => array($this, 'block_body'),
            'page' => array($this, 'block_page'),
            'content' => array($this, 'block_content'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_ff257cec4c69a1611c3fa2fb810abb8e4910ba046acc0cf65640b788bfb4acc0 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_ff257cec4c69a1611c3fa2fb810abb8e4910ba046acc0cf65640b788bfb4acc0->enter($__internal_ff257cec4c69a1611c3fa2fb810abb8e4910ba046acc0cf65640b788bfb4acc0_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@IUTOLivret/Default/index.html.twig"));

        $__internal_17a2b98c4efdc32cbb6535b3929b8c6772feb06bb574856abcefb9e3ab8e7fa4 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_17a2b98c4efdc32cbb6535b3929b8c6772feb06bb574856abcefb9e3ab8e7fa4->enter($__internal_17a2b98c4efdc32cbb6535b3929b8c6772feb06bb574856abcefb9e3ab8e7fa4_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@IUTOLivret/Default/index.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html lang=\"fr\">
<head>
    <meta charset=\"utf-8\">
    <title> ";
        // line 5
        $this->displayBlock('title', $context, $blocks);
        echo " </title>
    ";
        // line 6
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 9
        echo "</head>
<body>
";
        // line 11
        $this->displayBlock('body', $context, $blocks);
        // line 25
        $this->displayBlock('javascripts', $context, $blocks);
        // line 26
        echo "</body>
</html>
";
        
        $__internal_ff257cec4c69a1611c3fa2fb810abb8e4910ba046acc0cf65640b788bfb4acc0->leave($__internal_ff257cec4c69a1611c3fa2fb810abb8e4910ba046acc0cf65640b788bfb4acc0_prof);

        
        $__internal_17a2b98c4efdc32cbb6535b3929b8c6772feb06bb574856abcefb9e3ab8e7fa4->leave($__internal_17a2b98c4efdc32cbb6535b3929b8c6772feb06bb574856abcefb9e3ab8e7fa4_prof);

    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        $__internal_4405adf98ff38aea5d3651be0c20167f0d5e12b421a8a4b18251379af115b862 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_4405adf98ff38aea5d3651be0c20167f0d5e12b421a8a4b18251379af115b862->enter($__internal_4405adf98ff38aea5d3651be0c20167f0d5e12b421a8a4b18251379af115b862_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        $__internal_f90f12168148b892813e2c5981e86c00a96dad1c871e5a32d03652dffe3f997b = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_f90f12168148b892813e2c5981e86c00a96dad1c871e5a32d03652dffe3f997b->enter($__internal_f90f12168148b892813e2c5981e86c00a96dad1c871e5a32d03652dffe3f997b_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        echo " Accueil ";
        
        $__internal_f90f12168148b892813e2c5981e86c00a96dad1c871e5a32d03652dffe3f997b->leave($__internal_f90f12168148b892813e2c5981e86c00a96dad1c871e5a32d03652dffe3f997b_prof);

        
        $__internal_4405adf98ff38aea5d3651be0c20167f0d5e12b421a8a4b18251379af115b862->leave($__internal_4405adf98ff38aea5d3651be0c20167f0d5e12b421a8a4b18251379af115b862_prof);

    }

    // line 6
    public function block_stylesheets($context, array $blocks = array())
    {
        $__internal_b6269d32f9b14dde3663f0489ebd711442813d7219b1ff06d793d1885f9360fe = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_b6269d32f9b14dde3663f0489ebd711442813d7219b1ff06d793d1885f9360fe->enter($__internal_b6269d32f9b14dde3663f0489ebd711442813d7219b1ff06d793d1885f9360fe_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_8196cd9faae22e61a31170af079215642641b22db5c9c0ce52076d9e29f0c19e = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_8196cd9faae22e61a31170af079215642641b22db5c9c0ce52076d9e29f0c19e->enter($__internal_8196cd9faae22e61a31170af079215642641b22db5c9c0ce52076d9e29f0c19e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 7
        echo "        <link rel=\"stylesheet\" type=\"text/css\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("assets/css/src.css"), "html", null, true);
        echo "\">
    ";
        
        $__internal_8196cd9faae22e61a31170af079215642641b22db5c9c0ce52076d9e29f0c19e->leave($__internal_8196cd9faae22e61a31170af079215642641b22db5c9c0ce52076d9e29f0c19e_prof);

        
        $__internal_b6269d32f9b14dde3663f0489ebd711442813d7219b1ff06d793d1885f9360fe->leave($__internal_b6269d32f9b14dde3663f0489ebd711442813d7219b1ff06d793d1885f9360fe_prof);

    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        $__internal_11f95401895148c95155174d7833ba40e7be090c5fc6636888435dcb1bef9e0b = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_11f95401895148c95155174d7833ba40e7be090c5fc6636888435dcb1bef9e0b->enter($__internal_11f95401895148c95155174d7833ba40e7be090c5fc6636888435dcb1bef9e0b_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        $__internal_683ba158142e049939c617ed64f7d85a74a91eda105051f90137f3c7eeec97a4 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_683ba158142e049939c617ed64f7d85a74a91eda105051f90137f3c7eeec97a4->enter($__internal_683ba158142e049939c617ed64f7d85a74a91eda105051f90137f3c7eeec97a4_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 12
        echo "    <div></div>
    ";
        // line 13
        $this->displayBlock('page', $context, $blocks);
        
        $__internal_683ba158142e049939c617ed64f7d85a74a91eda105051f90137f3c7eeec97a4->leave($__internal_683ba158142e049939c617ed64f7d85a74a91eda105051f90137f3c7eeec97a4_prof);

        
        $__internal_11f95401895148c95155174d7833ba40e7be090c5fc6636888435dcb1bef9e0b->leave($__internal_11f95401895148c95155174d7833ba40e7be090c5fc6636888435dcb1bef9e0b_prof);

    }

    public function block_page($context, array $blocks = array())
    {
        $__internal_95a1f1b58a2d245043f9cd1687aebe43a8171d352b0a66324d3c815c9903c3ca = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_95a1f1b58a2d245043f9cd1687aebe43a8171d352b0a66324d3c815c9903c3ca->enter($__internal_95a1f1b58a2d245043f9cd1687aebe43a8171d352b0a66324d3c815c9903c3ca_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "page"));

        $__internal_fbaf480fba63b110c4a180856bfbd4bc9f5204761596ec828553492a8ddf2f5c = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_fbaf480fba63b110c4a180856bfbd4bc9f5204761596ec828553492a8ddf2f5c->enter($__internal_fbaf480fba63b110c4a180856bfbd4bc9f5204761596ec828553492a8ddf2f5c_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "page"));

        // line 14
        echo "        <div id=\"content\">
            ";
        // line 15
        $this->displayBlock('content', $context, $blocks);
        // line 22
        echo "        </div>
    ";
        
        $__internal_fbaf480fba63b110c4a180856bfbd4bc9f5204761596ec828553492a8ddf2f5c->leave($__internal_fbaf480fba63b110c4a180856bfbd4bc9f5204761596ec828553492a8ddf2f5c_prof);

        
        $__internal_95a1f1b58a2d245043f9cd1687aebe43a8171d352b0a66324d3c815c9903c3ca->leave($__internal_95a1f1b58a2d245043f9cd1687aebe43a8171d352b0a66324d3c815c9903c3ca_prof);

    }

    // line 15
    public function block_content($context, array $blocks = array())
    {
        $__internal_fae0274f80a2f7086f4a85e0b820cf8881a340ab11eb7819b7211953b2e531cc = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_fae0274f80a2f7086f4a85e0b820cf8881a340ab11eb7819b7211953b2e531cc->enter($__internal_fae0274f80a2f7086f4a85e0b820cf8881a340ab11eb7819b7211953b2e531cc_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "content"));

        $__internal_736523d9be96ba1899e15080dec58e616a23d7a6d0335ad28556fb5b6eef0c6b = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_736523d9be96ba1899e15080dec58e616a23d7a6d0335ad28556fb5b6eef0c6b->enter($__internal_736523d9be96ba1899e15080dec58e616a23d7a6d0335ad28556fb5b6eef0c6b_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "content"));

        // line 16
        echo "                <h1 class=\"title_accueil\"> Bienvenue sur le générateur de livret de projets </h1>
                <div class=\"\">
                    <button type=\"button\">CAS User</button>
                    <button type=\"button\">Public</button>
                </div>
            ";
        
        $__internal_736523d9be96ba1899e15080dec58e616a23d7a6d0335ad28556fb5b6eef0c6b->leave($__internal_736523d9be96ba1899e15080dec58e616a23d7a6d0335ad28556fb5b6eef0c6b_prof);

        
        $__internal_fae0274f80a2f7086f4a85e0b820cf8881a340ab11eb7819b7211953b2e531cc->leave($__internal_fae0274f80a2f7086f4a85e0b820cf8881a340ab11eb7819b7211953b2e531cc_prof);

    }

    // line 25
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_a7ee2417d7d681cb04552b57b3e5c3048419b81a6226c93552044ebf952a8a4f = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_a7ee2417d7d681cb04552b57b3e5c3048419b81a6226c93552044ebf952a8a4f->enter($__internal_a7ee2417d7d681cb04552b57b3e5c3048419b81a6226c93552044ebf952a8a4f_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_6b3eff71fad8d42c474ad2f45a33123465cdb7e08393792e2c95e09418a792ee = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_6b3eff71fad8d42c474ad2f45a33123465cdb7e08393792e2c95e09418a792ee->enter($__internal_6b3eff71fad8d42c474ad2f45a33123465cdb7e08393792e2c95e09418a792ee_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        
        $__internal_6b3eff71fad8d42c474ad2f45a33123465cdb7e08393792e2c95e09418a792ee->leave($__internal_6b3eff71fad8d42c474ad2f45a33123465cdb7e08393792e2c95e09418a792ee_prof);

        
        $__internal_a7ee2417d7d681cb04552b57b3e5c3048419b81a6226c93552044ebf952a8a4f->leave($__internal_a7ee2417d7d681cb04552b57b3e5c3048419b81a6226c93552044ebf952a8a4f_prof);

    }

    public function getTemplateName()
    {
        return "@IUTOLivret/Default/index.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  173 => 25,  158 => 16,  149 => 15,  138 => 22,  136 => 15,  133 => 14,  115 => 13,  112 => 12,  103 => 11,  90 => 7,  81 => 6,  63 => 5,  51 => 26,  49 => 25,  47 => 11,  43 => 9,  41 => 6,  37 => 5,  31 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("<!DOCTYPE html>
<html lang=\"fr\">
<head>
    <meta charset=\"utf-8\">
    <title> {% block title %} Accueil {% endblock %} </title>
    {% block stylesheets %}
        <link rel=\"stylesheet\" type=\"text/css\" href=\"{{ asset('assets/css/src.css') }}\">
    {% endblock %}
</head>
<body>
{% block body %}
    <div></div>
    {% block page %}
        <div id=\"content\">
            {% block content %}
                <h1 class=\"title_accueil\"> Bienvenue sur le générateur de livret de projets </h1>
                <div class=\"\">
                    <button type=\"button\">CAS User</button>
                    <button type=\"button\">Public</button>
                </div>
            {% endblock %}
        </div>
    {% endblock %}
{% endblock %}
{% block javascripts %}{% endblock %}
</body>
</html>
", "@IUTOLivret/Default/index.html.twig", "/home/zoe/www/livretO/projet_livretO_2A/src/IUTO/LivretBundle/Resources/views/Default/index.html.twig");
    }
}
