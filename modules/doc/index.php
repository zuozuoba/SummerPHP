<?php
class _index extends Main
{
	public $userinfo = array();

	public function initc()
	{
		View::$arrTplVar['amazeui'] = BASEURL . 'amazeui/';
		View::$arrTplVar['docui'] = BASEURL . 'sampledoc/';

		View::preshow('header');
		View::preshow('sidebar');
		View::endshow('footer');
	}

	public function index()
	{
		View::display('content');
	}

	public function home()
	{
		View::display('content');
	}

	public function hello()
	{
		View::show();
	}

	public function controller()
	{
		View::show();
	}

	public function router()
	{
		View::show('route');
	}

	public function maind()
	{
		View::show('main');
	}

	public function db_where()
	{
		View::show();
	}

	public function db_add()
	{
		View::show('db_insert');
	}

	public function db_get()
	{
		View::show();
	}

	public function db_up_del()
	{
		View::show();
	}

	public function safe()
	{
		View::show();
	}

	public function db()
	{
		View::show();
	}

	public function db_conf()
	{
		View::show();
	}

	public function other_getdoc()
	{
		View::show();
	}

    public function other_fun()
    {
        View::show();
    }

	public function view_base()
	{
		View::show();
	}

	public function view_pre()
	{
		View::show();
	}

    public function view_static()
    {
        View::show('view_css');
    }

}