resource "aws_s3_bucket" "wordpress_assets" {
  bucket = "${var.project_name}-assets"

  tags = {
    Name        = "${var.project_name}-assets"
    Project     = var.project_name
    Environment = "local"
    ManagedBy   = "Terraform"
  }
}

resource "aws_s3_bucket_versioning" "wordpress_assets" {
  bucket = aws_s3_bucket.wordpress_assets.id

  versioning_configuration {
    status = "Enabled"
  }
}

resource "aws_s3_object" "readme" {
  bucket = aws_s3_bucket.wordpress_assets.id
  key    = "README.txt"

  content = "Bucket S3 criado e gerenciado pelo Terraform através do LocalStack."
}
