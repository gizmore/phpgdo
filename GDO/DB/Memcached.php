<?php
/**
 * A non-operation memcached shim.
 * When you don't have memcached installed.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.2.0
 */
final class Memcached
{
	public function addServer() {}
	public function get() { return false; }
	public function set() { return false; }
	public function replace() { return false; }
	public function delete() { return false; }
	public function flush() { return false; }

}
