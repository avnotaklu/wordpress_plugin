#!/usr/bin/env node

const fs = require( 'fs' );
const path = require( 'path' );
const os = require( 'os' );
const { execFileSync } = require( 'child_process' );

const rootDir = path.resolve( __dirname, '..' );
const packageName = 'cal-id-embed';
const stagingRoot = path.join( os.tmpdir(), `${ packageName }-release` );
const stagingDir = path.join( stagingRoot, packageName );
const zipPath = path.join( rootDir, `${ packageName }.zip` );
const shouldZip = process.argv.includes( '--zip' );

const includeFiles = [
	'cal-id-embed.php',
	'uninstall.php',
	'readme.txt',
	'composer.json',
	'package.json',
	'phpcs.xml',
	'phpunit.xml',
];

const includeDirs = [ 'build', 'includes', 'src', 'languages' ];

const excludeNames = new Set( [
	'.git',
	'.github',
	'node_modules',
	'vendor',
	'tests',
	'scripts',
	'coverage',
	'build',
	'.wp-env',
] );

function shouldExclude( entry ) {
	return entry.startsWith( '.' ) || excludeNames.has( entry );
}

function removePath( target ) {
	fs.rmSync( target, { recursive: true, force: true } );
}

function copyItem( source, destination ) {
	const stat = fs.lstatSync( source );

	if ( stat.isDirectory() ) {
		fs.mkdirSync( destination, { recursive: true } );
		for ( const entry of fs.readdirSync( source ) ) {
			if ( shouldExclude( entry ) ) {
				continue;
			}

			copyItem(
				path.join( source, entry ),
				path.join( destination, entry )
			);
		}
		return;
	}

	fs.copyFileSync( source, destination );
}

removePath( stagingRoot );
fs.mkdirSync( stagingDir, { recursive: true } );

for ( const file of includeFiles ) {
	const source = path.join( rootDir, file );
	if ( fs.existsSync( source ) ) {
		copyItem( source, path.join( stagingDir, file ) );
	}
}

for ( const dir of includeDirs ) {
	const source = path.join( rootDir, dir );
	if ( fs.existsSync( source ) ) {
		copyItem( source, path.join( stagingDir, dir ) );
	}
}

if ( shouldZip ) {
	removePath( zipPath );
	execFileSync( 'zip', [ '-r', zipPath, packageName ], {
		cwd: stagingRoot,
		stdio: 'inherit',
	} );
}

// eslint-disable-next-line no-console
console.log( stagingDir );
